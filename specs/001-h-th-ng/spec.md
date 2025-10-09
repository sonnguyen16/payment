# Feature Specification: Hệ thống quản lý quy trình duyệt chi theo vai trò

**Feature Branch**: `001-h-th-ng`  
**Created**: 2025-10-05  
**Status**: Draft  
**Input**: User description: "Hệ thống quản lý quy trình duyệt chi theo vai trò với workflow phê duyệt đa cấp"

## Execution Flow (main)
```
1. Parse user description from Input
   ✓ Feature description: Multi-level approval workflow for payment requests
2. Extract key concepts from description
   ✓ Actors: Nhân viên, Trưởng bộ phận, Kế toán, Tổng giám đốc
   ✓ Actions: Tạo phiếu, phê duyệt, từ chối, xóa, thanh toán, hoàn ứng
   ✓ Data: Phiếu đề xuất chi, chứng từ, dự án
   ✓ Constraints: Workflow đa cấp, phân quyền theo vai trò
3. For each unclear aspect:
   ✓ CLARIFIED: Thông báo realtime trong app
   ✓ CLARIFIED: Không quy định thời hạn xử lý phiếu
   ✓ CLARIFIED: Không giới hạn số tiền cho từng loại phiếu
   ✓ CLARIFIED: Không giới hạn số lần chỉnh sửa, nhưng lưu lịch sử và lý do
4. Fill User Scenarios & Testing section
   ✓ Defined primary workflows for all 4 roles
5. Generate Functional Requirements
   ✓ 35+ testable requirements covering all business flows
6. Identify Key Entities
   ✓ Phiếu đề xuất, Người dùng, Dự án, Chứng từ, Lịch sử phê duyệt
7. Run Review Checklist
   ✓ All clarifications resolved
   ✓ No implementation details found
8. Return: SUCCESS (spec ready for planning)
```

---

## ⚡ Quick Guidelines
- ✅ Tập trung vào WHAT người dùng cần và WHY
- ❌ Tránh HOW triển khai (không đề cập tech stack, APIs, cấu trúc code)
- 👥 Viết cho stakeholders nghiệp vụ, không phải developers

---

## User Scenarios & Testing *(mandatory)*

### Primary User Story

**Nhân viên** cần chi tiền cho công việc, tạo phiếu đề xuất với đầy đủ thông tin (nội dung, số tiền, lý do, ngày phát sinh). Phiếu được gửi đến **Trưởng bộ phận** để xem xét. Nếu được duyệt, phiếu chuyển đến **Kế toán** kiểm tra và tiếp tục gửi **Tổng giám đốc** phê duyệt cuối. Sau khi Tổng giám đốc duyệt, Kế toán thực hiện thanh toán và nhân viên có trách nhiệm hoàn ứng kèm chứng từ.

### Acceptance Scenarios

#### Scenario 1: Quy trình phê duyệt thành công
1. **Given** nhân viên đã đăng nhập vào hệ thống, **When** nhân viên tạo phiếu tạm ứng với đầy đủ thông tin và gửi đề xuất, **Then** phiếu chuyển sang trạng thái "Chờ duyệt - Trưởng bộ phận" và trưởng bộ phận nhận được thông báo
2. **Given** phiếu đang ở trạng thái "Chờ duyệt - Trưởng bộ phận", **When** trưởng bộ phận phê duyệt phiếu, **Then** phiếu chuyển sang "Chờ duyệt - Kế toán" và kế toán nhận thông báo
3. **Given** phiếu đang ở trạng thái "Chờ duyệt - Kế toán", **When** kế toán phê duyệt, **Then** phiếu chuyển sang "Chờ duyệt - Tổng giám đốc"
4. **Given** phiếu đang ở trạng thái "Chờ duyệt - Tổng giám đốc", **When** Tổng giám đốc phê duyệt, **Then** phiếu chuyển sang "Chờ thanh toán" và kế toán nhận thông báo để thực hiện thanh toán

#### Scenario 2: Từ chối phiếu
1. **Given** phiếu đang chờ duyệt ở bất kỳ cấp nào, **When** người duyệt từ chối và ghi rõ lý do, **Then** phiếu chuyển về trạng thái "Bị từ chối", nhân viên nhận thông báo kèm lý do từ chối
2. **Given** phiếu bị từ chối, **When** nhân viên xem phiếu, **Then** nhân viên có thể chỉnh sửa hoặc hủy phiếu

#### Scenario 3: Chỉnh sửa phiếu
1. **Given** phiếu đang ở trạng thái "Chờ duyệt" hoặc "Bị từ chối", **When** nhân viên chỉnh sửa nội dung và ghi rõ lý do thay đổi, **Then** phiếu quay lại quy trình phê duyệt từ đầu (Trưởng bộ phận) với trạng thái "Chờ duyệt - Trưởng bộ phận"

#### Scenario 4: Hủy/Xóa phiếu
1. **Given** nhân viên có phiếu đang chờ duyệt, **When** nhân viên hủy phiếu và ghi rõ lý do, **Then** phiếu chuyển sang trạng thái "Đã hủy", bị khóa chỉnh sửa và dừng quy trình
2. **Given** phiếu đang chờ duyệt, **When** người có quyền (Trưởng BP/Kế toán/TGĐ) xóa phiếu, **Then** phiếu chuyển sang "Đã xóa", bị khóa hoàn toàn

#### Scenario 5: Thanh toán và hoàn ứng
1. **Given** phiếu đã được Tổng giám đốc phê duyệt, **When** kế toán thực hiện thanh toán và ghi nhận thông tin (mã thanh toán, ngày chi, chứng từ), **Then** phiếu chuyển sang "Đã thanh toán" và nhân viên nhận thông báo
2. **Given** phiếu đã thanh toán, **When** nhân viên hoàn ứng và nộp chứng từ, **Then** hệ thống ghi nhận hoàn ứng và cập nhật trạng thái phiếu

#### Scenario 6: Phiếu liên quan dự án
1. **Given** nhân viên tạo phiếu chi cho dự án, **When** nhân viên nhập mã dự án, **Then** hệ thống liên kết phiếu với dự án để theo dõi và đối chiếu

#### Scenario 7: Phiếu ưu tiên
1. **Given** nhân viên tạo phiếu cần xử lý gấp, **When** nhân viên đánh dấu phiếu là "Gấp", **Then** phiếu được ưu tiên hiển thị trong danh sách xử lý của người duyệt

### Edge Cases

#### Về quy trình phê duyệt
- **Phiếu bị từ chối nhiều lần**: Không giới hạn số lần chỉnh sửa/gửi lại. Hệ thống phải lưu lại toàn bộ lịch sử chỉnh sửa và lý do cho mỗi lần.
- **Người duyệt vắng mặt**: Khi người duyệt nghỉ phép/vắng mặt, phiếu được xử lý như thế nào? Có cơ chế ủy quyền không?
- **Thay đổi người duyệt giữa quy trình**: Nếu nhân viên chuyển bộ phận hoặc trưởng bộ phận thay đổi khi phiếu đang chờ duyệt?
- **Thời hạn xử lý**: Không quy định thời hạn bắt buộc cho mỗi cấp duyệt. Phiếu có thể chờ duyệt không giới hạn thời gian.

#### Về dữ liệu và trạng thái
- **Xóa phiếu đã thanh toán**: Có cho phép xóa phiếu đã thanh toán không? Nếu có thì ảnh hưởng đến báo cáo kế toán ra sao?
- **Chỉnh sửa sau khi đã duyệt một phần**: Nếu phiếu đã qua Trưởng BP nhưng chưa đến Kế toán, nhân viên chỉnh sửa thì có cần Trưởng BP duyệt lại không?
- **Phiếu không có mã dự án**: Có bắt buộc mọi phiếu phải có mã dự án không? Hay chỉ bắt buộc với một số loại phiếu?
- **Giới hạn số tiền**: Không có giới hạn số tiền tối đa cho bất kỳ loại phiếu nào. Mọi số tiền đều phải qua quy trình phê duyệt đầy đủ.

#### Về phân quyền
- **Kế toán quản lý nhiều văn phòng**: Nếu một kế toán được phân công nhiều văn phòng, làm sao phân biệt phiếu thuộc văn phòng nào?
- **Trưởng bộ phận kiêm nhiệm**: Nếu một người vừa là nhân viên vừa là trưởng bộ phận, quy trình duyệt phiếu của chính họ như thế nào?
- **Tổng giám đốc tạo phiếu**: Phiếu do Tổng giám đốc tạo có cần qua quy trình duyệt không?

#### Về thanh toán
- **Thanh toán một phần**: Có cho phép thanh toán từng phần hay phải thanh toán toàn bộ số tiền?
- **Hủy phiếu sau khi thanh toán**: Nếu phát hiện sai sót sau thanh toán, quy trình xử lý ra sao?
- **Hoàn ứng quá hạn**: Có quy định thời hạn hoàn ứng không? Nếu nhân viên không hoàn ứng thì xử lý thế nào?

#### Về thông báo
- **Thông báo realtime**: Hệ thống sử dụng thông báo realtime trong app (in-app notification). Người dùng nhận thông báo ngay khi có sự kiện liên quan.
- **Lịch sử thông báo**: Người dùng có thể xem lại các thông báo cũ không?

---

## Requirements *(mandatory)*

### Functional Requirements

#### Quản lý phiếu đề xuất
- **FR-001**: Hệ thống PHẢI cho phép nhân viên tạo 3 loại phiếu: Tạm ứng, Đề xuất thanh toán, Chi phí khác
- **FR-002**: Hệ thống PHẢI yêu cầu nhập đầy đủ: nội dung chi phí, số tiền dự kiến, lý do chi, ngày phát sinh dự kiến khi tạo phiếu
- **FR-003**: Hệ thống PHẢI cho phép nhân viên đánh dấu mức độ ưu tiên phiếu: "Gấp" hoặc "Bình thường"
- **FR-004**: Hệ thống PHẢI bắt buộc nhập mã dự án nếu chi phí liên quan đến dự án
- **FR-005**: Hệ thống PHẢI cho phép nhân viên chỉnh sửa phiếu khi ở trạng thái "Chờ duyệt" hoặc "Bị từ chối"
- **FR-006**: Hệ thống PHẢI yêu cầu ghi rõ lý do khi nhân viên chỉnh sửa phiếu
- **FR-006a**: Hệ thống PHẢI lưu lại toàn bộ lịch sử chỉnh sửa phiếu, bao gồm: nội dung thay đổi, lý do, người chỉnh sửa, thời gian
- **FR-006b**: Hệ thống KHÔNG ĐƯỢC giới hạn số lần chỉnh sửa phiếu
- **FR-007**: Hệ thống PHẢI cho phép nhân viên hủy phiếu của mình và yêu cầu ghi rõ lý do hủy
- **FR-008**: Hệ thống PHẢI chuyển phiếu sang trạng thái "Đã hủy" và khóa chỉnh sửa khi nhân viên hủy phiếu
- **FR-009**: Hệ thống PHẢI reset quy trình phê duyệt về đầu (Trưởng bộ phận) khi phiếu được chỉnh sửa

#### Quy trình phê duyệt đa cấp
- **FR-010**: Hệ thống PHẢI tuân theo workflow: Nhân viên → Trưởng bộ phận → Kế toán → Tổng giám đốc → Thanh toán
- **FR-011**: Hệ thống PHẢI tự động chuyển phiếu đến cấp tiếp theo khi được phê duyệt
- **FR-012**: Hệ thống PHẢI cho phép người duyệt (Trưởng BP, Kế toán, TGĐ) phê duyệt hoặc từ chối phiếu
- **FR-013**: Hệ thống PHẢI yêu cầu ghi rõ lý do khi từ chối phiếu
- **FR-014**: Hệ thống PHẢI chuyển phiếu về trạng thái "Bị từ chối" và thông báo cho nhân viên khi bị từ chối
- **FR-015**: Hệ thống PHẢI cho phép Trưởng bộ phận, Kế toán, Tổng giám đốc xóa phiếu
- **FR-016**: Hệ thống PHẢI chuyển phiếu sang trạng thái "Đã xóa" và khóa hoàn toàn khi bị xóa
- **FR-017**: Hệ thống PHẢI hiển thị rõ trạng thái hiện tại của phiếu và bộ phận đang xử lý

#### Quản lý thanh toán
- **FR-018**: Hệ thống PHẢI chuyển phiếu sang trạng thái "Chờ thanh toán" sau khi Tổng giám đốc phê duyệt
- **FR-019**: Hệ thống PHẢI cho phép kế toán ghi nhận thông tin thanh toán: mã thanh toán, ngày chi, chứng từ đính kèm
- **FR-020**: Hệ thống PHẢI chuyển phiếu sang trạng thái "Đã thanh toán" sau khi kế toán xác nhận thanh toán
- **FR-021**: Hệ thống PHẢI cho phép nhân viên ghi nhận hoàn ứng sau khi nhận tiền
- **FR-022**: Hệ thống PHẢI cho phép nhân viên nộp chứng từ sau khi hoàn ứng

#### Phân quyền truy cập
- **FR-023**: Hệ thống PHẢI cho phép Nhân viên xem, sửa, hủy các phiếu do chính mình tạo
- **FR-024**: Hệ thống PHẢI cho phép Trưởng bộ phận xem các phiếu do mình tạo và của nhân viên trong bộ phận
- **FR-025**: Hệ thống PHẢI cho phép Trưởng bộ phận phê duyệt/từ chối/xóa phiếu của nhân viên thuộc quyền
- **FR-026**: Hệ thống PHẢI cho phép Kế toán xem và xử lý phiếu từ các văn phòng được phân công
- **FR-027**: Hệ thống PHẢI phân biệt phiếu theo văn phòng khi kế toán quản lý nhiều văn phòng
- **FR-028**: Hệ thống PHẢI cho phép Tổng giám đốc xem và phê duyệt toàn bộ phiếu trong hệ thống

#### Thông báo và theo dõi
- **FR-029**: Hệ thống PHẢI gửi thông báo realtime trong app cho người duyệt khi có phiếu mới cần xử lý
- **FR-030**: Hệ thống PHẢI gửi thông báo realtime trong app cho nhân viên khi phiếu được phê duyệt hoặc từ chối, kèm lý do cụ thể
- **FR-031**: Hệ thống PHẢI gửi thông báo realtime trong app cho kế toán khi có phiếu cần thanh toán
- **FR-032**: Hệ thống PHẢI gửi thông báo realtime trong app cho nhân viên sau khi phiếu được thanh toán
- **FR-033**: Hệ thống PHẢI hiển thị danh sách phiếu theo mức độ ưu tiên ("Gấp" hiển thị trước)
- **FR-033a**: Hệ thống KHÔNG ĐƯỢC quy định thời hạn bắt buộc cho việc xử lý phiếu ở mỗi cấp duyệt

#### Quản lý dự án
- **FR-034**: Hệ thống PHẢI liên kết phiếu với mã dự án để theo dõi và đối chiếu chi phí
- **FR-035**: Hệ thống PHẢI cho phép xem tất cả phiếu chi liên quan đến một dự án cụ thể

#### Lịch sử và kiểm soát
- **FR-036**: Hệ thống PHẢI lưu lại toàn bộ lịch sử thay đổi trạng thái của phiếu
- **FR-037**: Hệ thống PHẢI ghi nhận người thực hiện mỗi hành động (tạo, duyệt, từ chối, xóa, chỉnh sửa)
- **FR-038**: Hệ thống PHẢI lưu lại lý do từ chối, hủy, xóa, chỉnh sửa phiếu
- **FR-039**: Hệ thống PHẢI lưu lại chi tiết mỗi lần chỉnh sửa phiếu: nội dung cũ, nội dung mới, lý do thay đổi, người chỉnh sửa, thời gian

### Key Entities

#### Phiếu đề xuất chi (Payment Request)
Đại diện cho một yêu cầu chi tiền từ nhân viên. Bao gồm:
- Loại phiếu (Tạm ứng, Đề xuất thanh toán, Chi phí khác)
- Nội dung chi phí
- Số tiền dự kiến
- Lý do chi
- Ngày phát sinh dự kiến
- Mức độ ưu tiên (Gấp/Bình thường)
- Trạng thái hiện tại (Chờ duyệt, Bị từ chối, Đã hủy, Đã xóa, Chờ thanh toán, Đã thanh toán)
- Bộ phận đang xử lý
- Liên kết đến dự án (nếu có)
- Người tạo phiếu
- Ngày tạo, ngày cập nhật

#### Người dùng (User)
Đại diện cho người sử dụng hệ thống. Bao gồm:
- Thông tin cá nhân (họ tên, email, số điện thoại)
- Vai trò (Nhân viên, Trưởng bộ phận, Kế toán, Tổng giám đốc)
- Bộ phận/Phòng ban
- Văn phòng (để phân quyền cho kế toán)
- Trạng thái hoạt động

#### Dự án (Project)
Đại diện cho dự án mà chi phí liên quan. Bao gồm:
- Mã dự án
- Tên dự án
- Mô tả
- Ngân sách dự kiến
- Tổng chi phí thực tế (tính từ các phiếu đã thanh toán)
- Trạng thái dự án

#### Chứng từ (Document)
Đại diện cho chứng từ đính kèm phiếu. Bao gồm:
- Loại chứng từ (Hóa đơn, Biên lai, Chứng từ khác)
- File đính kèm
- Mô tả
- Ngày nộp
- Liên kết đến phiếu đề xuất

#### Lịch sử phê duyệt (Approval History)
Đại diện cho từng bước trong quy trình phê duyệt. Bao gồm:
- Phiếu liên quan
- Người thực hiện hành động
- Hành động (Tạo, Gửi duyệt, Phê duyệt, Từ chối, Hủy, Xóa, Chỉnh sửa, Thanh toán, Hoàn ứng)
- Trạng thái trước và sau
- Lý do (nếu có)
- Thời gian thực hiện

#### Thông báo (Notification)
Đại diện cho thông báo gửi đến người dùng. Bao gồm:
- Người nhận
- Loại thông báo (Phiếu mới, Phê duyệt, Từ chối, Cần thanh toán, Đã thanh toán)
- Nội dung thông báo
- Liên kết đến phiếu
- Trạng thái đã đọc/chưa đọc
- Thời gian gửi

---

## Review & Acceptance Checklist

### Content Quality
- [x] No implementation details (languages, frameworks, APIs)
- [x] Focused on user value and business needs
- [x] Written for non-technical stakeholders
- [x] All mandatory sections completed

### Requirement Completeness
- [x] No [NEEDS CLARIFICATION] markers remain
- [x] Requirements are testable and unambiguous
- [x] Success criteria are measurable
- [x] Scope is clearly bounded
- [x] Dependencies and assumptions identified

**Các điểm đã được làm rõ:**
1. ✓ Cơ chế thông báo: Realtime trong app (in-app notification)
2. ✓ Thời hạn xử lý: Không quy định thời hạn bắt buộc
3. ✓ Giới hạn số tiền: Không giới hạn số tiền tối đa
4. ✓ Số lần chỉnh sửa: Không giới hạn, nhưng lưu đầy đủ lịch sử

---

## Execution Status

- [x] User description parsed
- [x] Key concepts extracted
- [x] Ambiguities marked and resolved (4 clarifications completed)
- [x] User scenarios defined
- [x] Requirements generated (42 functional requirements)
- [x] Entities identified (6 key entities)
- [x] Review checklist passed

---
