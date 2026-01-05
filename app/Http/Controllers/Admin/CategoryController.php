<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- 1. استيراد كلاس Rule للتحقق المتقدم

class CategoryController extends Controller
{
    /**
     * عرض جميع الفئات.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(15); 
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('admin.categories.index');
    }

    /**
     * تخزين فئة جديدة في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'حقل اسم الفئة مطلوب.',
            'name.unique' => 'هذه الفئة موجودة بالفعل.',
            'name.max' => 'يجب ألا يتجاوز اسم الفئة 255 حرفًا.',
        ]);

        Category::create($validatedData);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'تمت إضافة الفئة بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * عرض نموذج تعديل فئة محددة.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * ▼▼▼ هذه هي الدالة الجديدة والمبرمجة ▼▼▼
     * تحديث بيانات فئة محددة في قاعدة البيانات.
     */
    public function update(Request $request, Category $category) // <-- استخدام Route Model Binding
    {
        // 2. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            // قاعدة التحقق من التفرد (unique) أصبحت أكثر ذكاءً
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id), // تجاهل الفئة الحالية عند التحقق
            ],
        ], [
            'name.required' => 'حقل اسم الفئة مطلوب.',
            'name.unique' => 'هذه الفئة موجودة بالفعل.',
            'name.max' => 'يجب ألا يتجاوز اسم الفئة 255 حرفًا.',
        ]);

        // 3. تحديث بيانات الفئة
        $category->update($validatedData);

        // 4. إعادة التوجيه إلى صفحة الفئات مع رسالة نجاح
        return redirect()->route('admin.categories.index')
                         ->with('success', 'تم تحديث الفئة بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
        /**
     * ▼▼▼ هذه هي الدالة الجديدة والمبرمجة ▼▼▼
     * حذف فئة محددة من قاعدة البيانات.
     */
        /**
     * ▼▼▼ هذه هي الدالة الجديدة والمبرمجة (بمنطق الحذف القسري) ▼▼▼
     * حذف فئة محددة وتحديث الكتب المرتبطة بها.
     */
    public function destroy(Category $category)
    {
        // 1. ابحث عن جميع الكتب المرتبطة بهذه الفئة
        // 2. قم بتحديثها واجعل category_id = NULL
        $category->audiobooks()->update(['category_id' => null]);

        // 3. الآن، قم بحذف الفئة نفسها بأمان
        $category->delete();

        // 4. أرجع رسالة نجاح
        return redirect()->route('admin.categories.index')
                         ->with('success', 'تم حذف الفئة بنجاح، وأصبحت الكتب المرتبطة بها "بدون فئة".');
    }


}
