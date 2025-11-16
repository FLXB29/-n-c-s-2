<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','description','icon','color','image',
        'is_active', 'sort_order'
    ];//Đây là những thuộc tính được phép điền khi tạo mới hoặc cập nhật bản ghi
    protected $cats= [
        'is_active' => 'boolean',
    ];

    //Relationships
    public function events() {
        return $this->hasMany(Event::class);
    }

    //Scopes
    //Nếu muốn gọi hàm này chỉ cần bỏ scope đi và viết thường
    //chữ cái đầu tiên là được
    //Ví dụ: Category::active()->get();
    public function scopeActive($query){
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query){
        return $query->orderBy('sort_order') -> orderBy('name');
    }
}
