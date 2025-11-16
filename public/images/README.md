# Images Folder

Thư mục này chứa các hình ảnh cho website.

## Cấu trúc đề xuất:

```
images/
├── events/           # Ảnh sự kiện
├── banners/          # Ảnh banner
├── avatars/          # Avatar người dùng
├── categories/       # Icon danh mục
└── placeholders/     # Ảnh placeholder
```

## Hiện tại:

Project đang sử dụng placeholder images từ:
- **Unsplash** (https://images.unsplash.com/)
- **UI Avatars** (https://ui-avatars.com/)

## Khi production:

1. Upload ảnh thật vào các thư mục tương ứng
2. Thay thế các URL placeholder trong HTML
3. Optimize ảnh (compress, resize)
4. Sử dụng lazy loading cho performance
5. Có thể dùng CDN để tăng tốc độ load

## Kích thước đề xuất:

- **Event cards**: 500x300px (hoặc 16:9)
- **Event cover**: 1200x600px
- **Banner slides**: 1920x1080px
- **Avatars**: 200x200px
- **Category icons**: 100x100px
