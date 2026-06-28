# Bug List & Fixes
1. **L?i HTML Form Nested**: Th? <form> n?m trong th? <a> ? danh sách thông báo. Ðã s?a d?i <a> thành <div> trong dashboard_notifications.php.
2. **L?i URL Tampering (Ranking)**: Ch?nh s?a $id = (int); và xác nh?n b?n ghi t?n t?i tru?c khi c?p nh?t.
3. **Thi?u Validation (Ranking)**: Ðã thêm ki?m tra series_id t?n t?i, rank_position >= 1, và score (0-100) ? hàm store và update.
4. **Thi?u Method Check (Notification)**: Ðã gi?i h?n markAsRead và markAllAsRead ch? ch?p nh?n POST.
