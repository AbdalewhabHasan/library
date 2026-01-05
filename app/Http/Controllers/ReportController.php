<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * إضافة إبلاغ جديد (نسخة معدلة لتقبل كل أنواع الإبلاغات)
     */
    public function store(Request $request): JsonResponse
    {
        // 1. التحقق من صحة البيانات (أصبح أكثر مرونة)
        $validated = $request->validate([
            'reportable_type' => 'required|string',
            'reportable_id'   => 'required|integer',
            'reason_type'     => 'nullable|string|max:50', // هذا يأتي من الداشبورد (اختياري)
            'reason'          => 'nullable|string|max:500', // هذا يأتي من النوافذ الأخرى (اختياري)
        ]);

        // 2. التحقق من وجود الكيان المبلغ عنه
        $reportableClass = $validated['reportable_type'];
        if (!class_exists($reportableClass)) {
            return response()->json(['success' => false, 'message' => 'نوع المحتوى غير صالح.'], 400);
        }
        $reportable = app($reportableClass)->find($validated['reportable_id']);

        if (!$reportable) {
            return response()->json(['success' => false, 'message' => 'الكيان المبلغ عنه غير موجود.'], 404);
        }

        // 3. التحقق من عدم إبلاغ نفس المستخدم عن نفس الكيان مرتين
        $existingReport = Report::where('user_id', Auth::id())
            ->where('reportable_type', $validated['reportable_type'])
            ->where('reportable_id', $validated['reportable_id'])
            ->exists();

        if ($existingReport) {
            return response()->json(['success' => false, 'message' => 'لقد قمت بالإبلاغ عن هذا العنصر من قبل.'], 422);
        }

        // 4. إنشاء الإبلاغ
        Report::create([
            'user_id'         => Auth::id(),
            'reportable_type' => $validated['reportable_type'],
            'reportable_id'   => $validated['reportable_id'],
            'reason_type'     => $validated['reason_type'] ?? 'general_report', // قيمة افتراضية إذا لم يأتِ نوع
            'reason'          => $validated['reason'] ?? 'Reported for: ' . ($validated['reason_type'] ?? 'N/A'), // نص تلقائي
            'status'          => 'pending',
        ]);

        // 5. إرجاع رد ناجح
        return response()->json([
            'success' => true,
            'message' => 'شكراً على إبلاغك. سيتم مراجعة الإبلاغ من قبل فريقنا.'
        ]);
    }

    /**
     * عرض جميع الإبلاغات (للمشرف فقط).
     */
    public function index(Request $request)
    {
        $query = Report::with(['user', 'reportable'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('reportable_type')) {
            $query->where('reportable_type', $request->reportable_type);
        }
        if ($request->filled('reason_type')) {
            $query->where('reason_type', $request->reason_type);
        }

        $reports = $query->paginate(15);
        $reasonTypes = ['spam', 'inappropriate', 'copyright', 'misleading', 'low_quality', 'other', 'general_report'];

        return view('admin.reports.index', [
            'reports' => $reports,
            'selectedStatus' => $request->status ?? '',
            'selectedType' => $request->reportable_type ?? '',
            'selectedReason' => $request->reason_type ?? '',
            'reasonTypes' => $reasonTypes,
        ]);
    }

    /**
     * عرض تفاصيل إبلاغ معين (للمشرف فقط).
     */
    public function show(Report $report)
    {
        $report->load(['user', 'reportable']);
        return view('admin.reports.show', compact('report'));
    }

    /**
     * تحديث حالة الإبلاغ (للمشرف فقط).
     */
    public function updateStatus(Report $report, Request $request): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,reviewed,rejected']);
        $report->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'تم تحديث حالة الإبلاغ بنجاح.']);
    }

    /**
     * حذف إبلاغ (للمشرف فقط).
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return back()->with('success', 'تم حذف الإبلاغ بنجاح.');
    }
}
