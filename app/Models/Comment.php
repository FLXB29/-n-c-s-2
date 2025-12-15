<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'event_comments';

    protected $fillable = [
        'event_id',
        'user_id',
        'parent_id',
        'content',
        'rating',
        'status'
    ];

    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id')->with('user');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->approved()->with('replies', 'user');
    }

    // Đếm tổng số replies (bao gồm cả nested)
    public function getAllRepliesCountAttribute()
    {
        $count = $this->replies->count();
        foreach ($this->replies as $reply) {
            $count += $reply->all_replies_count;
        }
        return $count;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeMainComments($query)
    {
        return $query->whereNull('parent_id');
    }
}
