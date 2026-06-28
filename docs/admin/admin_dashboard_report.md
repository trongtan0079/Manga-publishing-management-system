# Admin Dashboard Report

## T?ng quan
Dashboard Admin hi?n th? toàn b? th?ng kê h? th?ng bao g?m:
- 9 Stat Cards chính: User, Series, Chapter, Page, Task, Submission, Review, Notification, Ranking.
- 3 Stat Cards ph?: Active Users, Inactive Users, Banned Users.
- 3 Bi?u d? Chart.js: User theo Role (Bar), Task theo Status (Doughnut), Submission theo Status (Doughnut).
- Widget thông báo g?n dây.

## D? li?u
- T?t c? d? li?u du?c truy v?n tr?c ti?p t? Database thông qua các Model.
- Không s? d?ng cache hay b?ng t?m.
- Bi?u d? s? d?ng Chart.js v4 qua CDN, không cài d?t package.

## Phân quy?n
- Ch? Admin m?i có quy?n truy c?p Dashboard Admin.
- S? d?ng `requireRole('admin')` d? ki?m tra quy?n.
