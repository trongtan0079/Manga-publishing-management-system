@startuml
title Activity Diagram - Quy Trình Xuất Bản Manga

skinparam backgroundColor white
skinparam shadowing false
skinparam linetype ortho
skinparam activityDiamondOrientation vertical

start

:Editorial Board nhập dữ liệu bình chọn;

:Hệ thống cập nhật Ranking;

:Xem thống kê hiệu suất Series;

:Xem báo cáo từ Tantou Editor;

if (Series đạt yêu cầu?) then (Có)

    :Tiếp tục xuất bản;

    :Cập nhật lịch phát hành;

else (Không)

    if (Ranking quá thấp?) then (Có)

        :Đề xuất huỷ Series;

        :Editorial Board bỏ phiếu;

        if (Huỷ Series?) then (Có)

            :Ngừng xuất bản;

        else (Không)

            :Tiếp tục theo dõi;

        endif

    else (Không)

        :Theo dõi thêm dữ liệu;

    endif

endif

stop

@enduml
