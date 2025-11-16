<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organizer_id', 'category_id', 'title', 'slug', 'description', 'short_description',
        'featured_image', 'gallery', 'start_datetime', 'end_datetime', 'timezone',
        'venue_name', 'venue_address', 'venue_city', 'latitude', 'longitude',
        'is_free', 'total_tickets', 'tickets_sold', 'min_price', 'max_price',
        'is_featured', 'allow_comments', 'status', 'visibility', 'view_count', 'like_count'
    ];

    protected $casts = [
        'gallery' => 'array',
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');//thuộc về một user khớp với organizer_id
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'published' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) {
            return 'Miễn phí';
        }
        
        if ($this->min_price == $this->max_price) {
            return number_format($this->min_price) . ' VNĐ';
        }
        
        return number_format($this->min_price) . ' - ' . number_format($this->max_price) . ' VNĐ';
    }
}