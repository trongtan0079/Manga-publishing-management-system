# Role-Based Access Control (RBAC) Permission Matrix

Tài liệu này thể hiện Ma trận phân quyền đối với các Module cốt lõi của hệ thống Manga Publishing Management System (Dựa trên Static Code Audit).

### Module / Controller: **Dashboard**
- **Admin**: View (Admin)
- **Mangaka**: View (Mangaka)
- **Assistant**: View (Assistant)
- **Editor**: View (Editor)
- **Editorial Board**: View (Board)
- **Guest**: Forbidden

### Module / Controller: **User Mngt**
- **Admin**: View, Edit, Create, Delete
- **Mangaka**: Forbidden
- **Assistant**: Forbidden
- **Editor**: Forbidden
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Series**
- **Admin**: Forbidden
- **Mangaka**: View, Create, Edit, Delete (Own only)
- **Assistant**: Forbidden
- **Editor**: Forbidden
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Chapter**
- **Admin**: Forbidden
- **Mangaka**: View, Create, Edit, Delete (Own only)
- **Assistant**: Forbidden
- **Editor**: Forbidden
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Page**
- **Admin**: Forbidden
- **Mangaka**: View, Create, Edit, Delete (Own only)
- **Assistant**: Forbidden
- **Editor**: Forbidden
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Task**
- **Admin**: Forbidden
- **Mangaka**: View, Create, Edit, Delete (Own only)
- **Assistant**: View, Update Status (Assigned only)
- **Editor**: Forbidden
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Submission**
- **Admin**: Forbidden
- **Mangaka**: Create (Own Chapter)
- **Assistant**: Create (Own Task)
- **Editor**: View (Pending)
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Review**
- **Admin**: Forbidden
- **Mangaka**: Create, View (Own Task)
- **Assistant**: Forbidden
- **Editor**: Create, View (Chapter)
- **Editorial Board**: Forbidden
- **Guest**: Forbidden

### Module / Controller: **Series Ranking**
- **Admin**: Forbidden
- **Mangaka**: Forbidden
- **Assistant**: Forbidden
- **Editor**: Forbidden
- **Editorial Board**: View, Create, Edit, Delete
- **Guest**: Forbidden

### Module / Controller: **Notification**
- **Admin**: Forbidden
- **Mangaka**: View, Edit (Own only)
- **Assistant**: View, Edit (Own only)
- **Editor**: View, Edit (Own only)
- **Editorial Board**: View, Edit (Own only)
- **Guest**: Forbidden

### Chú thích trạng thái:
- **View**: Được phép xem danh sách và chi tiết.
- **Create**: Được phép khởi tạo dữ liệu mới.
- **Edit**: Được phép cập nhật dữ liệu.
- **Delete**: Được phép xóa.
- **Forbidden**: Hoàn toàn bị cấm truy cập (Trả về 403 hoặc Redirect đi nơi khác).
- **(Own only)**: Kiểm tra Data Ownership cực kỳ nghiêm ngặt. Dữ liệu phải thuộc về quyền sở hữu (hoặc được giao nhiệm vụ) của người thực hiện thì mới truy cập được. Bất kỳ sự giả mạo URL ID nào sang dữ liệu của người khác đều bị chặn.
