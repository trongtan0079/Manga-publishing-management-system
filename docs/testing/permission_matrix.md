# Role-Based Access Control (RBAC) Permission Matrix

Tài liệu này thể hiện Ma trận phân quyền đối với các Module cốt lõi của hệ thống Manga Publishing Management System (Dựa trên Static Code Audit).

| Module / Controller | Admin | Mangaka | Assistant | Editor | Editorial Board | Guest |
|---------------------|-------|---------|-----------|--------|-----------------|-------|
| **Dashboard** | View (Admin) | View (Mangaka) | View (Assistant) | View (Editor) | View (Board) | Forbidden |
| **User Mngt** | View, Edit, Create, Delete | Forbidden | Forbidden | Forbidden | Forbidden | Forbidden |
| **Series** | Forbidden | View, Create, Edit, Delete (Own only) | Forbidden | Forbidden | Forbidden | Forbidden |
| **Chapter** | Forbidden | View, Create, Edit, Delete (Own only) | Forbidden | Forbidden | Forbidden | Forbidden |
| **Page** | Forbidden | View, Create, Edit, Delete (Own only) | Forbidden | Forbidden | Forbidden | Forbidden |
| **Task** | Forbidden | View, Create, Edit, Delete (Own only) | View, Update Status (Assigned only) | Forbidden | Forbidden | Forbidden |
| **Submission** | Forbidden | Create (Own Chapter) | Create (Own Task) | View (Pending) | Forbidden | Forbidden |
| **Review** | Forbidden | Create, View (Own Task) | Forbidden | Create, View (Chapter) | Forbidden | Forbidden |
| **Series Ranking** | Forbidden | Forbidden | Forbidden | Forbidden | View, Create, Edit, Delete | Forbidden |
| **Notification** | Forbidden | View, Edit (Own only) | View, Edit (Own only) | View, Edit (Own only) | View, Edit (Own only) | Forbidden |

### Chú thích trạng thái:
- **View**: Được phép xem danh sách và chi tiết.
- **Create**: Được phép khởi tạo dữ liệu mới.
- **Edit**: Được phép cập nhật dữ liệu.
- **Delete**: Được phép xóa.
- **Forbidden**: Hoàn toàn bị cấm truy cập (Trả về 403 hoặc Redirect đi nơi khác).
- **(Own only)**: Kiểm tra Data Ownership cực kỳ nghiêm ngặt. Dữ liệu phải thuộc về quyền sở hữu (hoặc được giao nhiệm vụ) của người thực hiện thì mới truy cập được. Bất kỳ sự giả mạo URL ID nào sang dữ liệu của người khác đều bị chặn.
