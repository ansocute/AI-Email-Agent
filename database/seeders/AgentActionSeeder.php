<?php

namespace Database\Seeders;

use App\Models\AgentAction;
use App\Models\Email;
use Illuminate\Database\Seeder;

class AgentActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = Email::all();

        if ($emails->isEmpty()) {
            return;
        }

        $mockData = [
            [
                'type' => 'draft_reply',
                'content' => "Chào anh Nguyễn,\nEm xác nhận tham gia cuộc họp quý 3 vào 9h sáng Thứ Sáu tuần tới. Em sẽ gửi agenda trước 1 ngày. Cảm ơn anh!",
                'status' => 'pending',
            ],
            [
                'type' => 'create_event',
                'content' => "Họp quý 3 với Team Đối tác\nThời gian: Thứ Sáu, 09:00 - 10:00\nĐịa điểm: Google Meet",
                'status' => 'pending',
            ],
            [
                'type' => 'draft_reply',
                'content' => "Chào ban tổ chức,\nTeam chúng tôi đã đăng ký tham gia teambuilding 5 người. Đã hoàn tất danh sách thành viên.",
                'status' => 'approved',
            ],
            [
                'type' => 'draft_reply',
                'content' => "Xin lỗi, hiện tại chúng tôi chưa có nhu cầu sử dụng voucher khuyến mãi.",
                'status' => 'rejected',
            ],
            [
                'type' => 'create_event',
                'content' => "Lịch xử lý sự cố khẩn cấp khách hàng VIP\nThời gian: Hôm nay, 14:00 - 15:00",
                'status' => 'pending',
            ],
        ];

        foreach ($mockData as $index => $data) {
            $email = $emails[$index % $emails->count()];
            AgentAction::firstOrCreate(
                [
                    'email_id' => $email->id,
                    'type' => $data['type'],
                    'content' => $data['content'],
                ],
                [
                    'status' => $data['status'],
                ]
            );
        }
    }
}
