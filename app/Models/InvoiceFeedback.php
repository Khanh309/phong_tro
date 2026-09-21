<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceFeedback extends Model
{
    use HasFactory;

    protected $table = 'invoice_feedbacks';

    protected $fillable = [
        'invoice_id',
        'tenant_id',
        'feedback_type',
        'content',
        'status',
        'admin_reply',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getFeedbackTypeNameAttribute(): string
    {
        return match ($this->feedback_type) {
            'electricity' => 'Thắc mắc chỉ số Điện',
            'water' => 'Thắc mắc chỉ số Nước',
            'fees' => 'Thắc mắc Phí dịch vụ',
            default => 'Ý kiến / Khiếu nại khác'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ chủ nhà phản hồi',
            'processing' => 'Đang kiểm tra lại',
            'resolved' => 'Đã giải quyết',
            'rejected' => 'Từ chối / Giữ nguyên',
            default => 'Chờ phản hồi'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'resolved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }
}
