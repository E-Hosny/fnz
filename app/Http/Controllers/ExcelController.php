<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExcelData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Exception;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function index()
    {
        $excelData = ExcelData::orderBy('created_at', 'desc')->get();
        
        // جمع جميع القيم الأصلية من جميع البيانات المحفوظة
        $originalValues = [];
        foreach ($excelData as $data) {
            $columnData = json_decode($data->cell_value, true);
            if ($columnData && is_array($columnData)) {
                foreach ($columnData as $item) {
                    $originalValues[] = trim($item['value']);
                }
            }
        }
        
        
        // إضافة معلومات التكرار لكل بيانات
        $excelData->each(function ($data) use ($originalValues) {
            $columnData = json_decode($data->cell_value, true);
            if ($columnData && is_array($columnData)) {
                foreach ($columnData as &$item) {
                    $value = trim($item['value']);
                    $reversedValue = $this->reverseText($value);
                    
                    // فحص التكرار للنص الأصلي
                    $originalCount = array_count_values($originalValues)[$value] ?? 0;
                    $isOriginalDuplicate = $originalCount > 1;
                    
                    // فحص التكرار للنص المعكوس
                    $isReversedDuplicate = false;
                    if ($reversedValue !== $value) {
                        foreach ($originalValues as $originalValue) {
                            if ($originalValue === $reversedValue) {
                                $isReversedDuplicate = true;
                                break;
                            }
                        }
                    }
                    
                    
                    $item['is_duplicate'] = $isOriginalDuplicate || $isReversedDuplicate;
                    $item['is_reversed'] = $isReversedDuplicate && !$isOriginalDuplicate;
                }
                $data->cell_value = json_encode($columnData);
            }
        });
        
        return view('home', compact('excelData'));
    }

    /**
     * عكس النص مع الحفاظ على الأرقام والرموز
     */
    private function reverseText($text)
    {
        // تقسيم النص إلى أجزاء مفصولة بـ - أو مسافات أو أي فاصل
        $parts = preg_split('/([-,\s]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $reversedParts = [];
        
        // عكس ترتيب الأجزاء (وليس عكس كل جزء منفرداً)
        for ($i = count($parts) - 1; $i >= 0; $i--) {
            $reversedParts[] = $parts[$i];
        }
        
        return implode('', $reversedParts);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'column_reference' => 'required|string|max:5|regex:/^[A-Z]+$/'
        ], [
            'excel_file.required' => 'يرجى اختيار ملف Excel',
            'excel_file.mimes' => 'يجب أن يكون الملف من نوع Excel (.xlsx أو .xls)',
            'excel_file.max' => 'حجم الملف يجب أن يكون أقل من 10 ميجابايت',
            'column_reference.required' => 'يرجى إدخال رمز العمود',
            'column_reference.regex' => 'رمز العمود يجب أن يكون بصيغة صحيحة (مثل: A, B, C)'
        ]);

        try {
            // حفظ الملف
            $file = $request->file('excel_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('excel_files', $fileName, 'public');

            // قراءة ملف Excel
            $spreadsheet = IOFactory::load(storage_path('app/public/' . $filePath));
            
            // استخدام الورقة الأولى تلقائياً
            $worksheet = $spreadsheet->getActiveSheet();
            $sheetName = $worksheet->getTitle();
            
            // قراءة جميع البيانات في العمود المحدد
            $columnData = [];
            $row = 1;
            $maxRows = $worksheet->getHighestRow();
            
            for ($row = 1; $row <= $maxRows; $row++) {
                $cellValue = $worksheet->getCell($request->column_reference . $row)->getCalculatedValue();
                if (!empty($cellValue)) {
                    $columnData[] = [
                        'row' => $row,
                        'value' => $cellValue
                    ];
                }
            }

            if (empty($columnData)) {
                Storage::disk('public')->delete($filePath);
                return back()->withErrors(['column_reference' => 'لا توجد بيانات في العمود المحدد']);
            }

            // حفظ البيانات في قاعدة البيانات
            $excelData = ExcelData::create([
                'file_name' => $file->getClientOriginalName(),
                'sheet_name' => $sheetName,
                'cell_reference' => $request->column_reference,
                'cell_value' => json_encode($columnData), // حفظ جميع البيانات كـ JSON
                'file_path' => $filePath
            ]);

            $dataCount = count($columnData);
            return back()->with('success', "تم رفع الملف وقراءة البيانات بنجاح! تم العثور على {$dataCount} صف في العمود {$request->column_reference}");

        } catch (Exception $e) {
            if (isset($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return back()->withErrors(['error' => 'حدث خطأ أثناء معالجة الملف: ' . $e->getMessage()]);
        }
    }


    public function delete($id)
    {
        $excelData = ExcelData::findOrFail($id);
        
        // حذف الملف من التخزين
        Storage::disk('public')->delete($excelData->file_path);
        
        // حذف السجل من قاعدة البيانات
        $excelData->delete();

        return back()->with('success', 'تم حذف البيانات بنجاح!');
    }
}
