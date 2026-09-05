{*
 * MOCKUP - GIAO DIỆN TĨNH, CHƯA CÓ CHATBOT THẬT
 *
 * Vỏ giao diện cho FR-14/15/16 (AI Chatbot), phục vụ demo cho khách. Không gọi
 * API, không lưu gì, không có mô hình ngôn ngữ nào phía sau. Tin nhắn gửi đi chỉ
 * hiện lại trên màn hình rồi nhận một câu trả lời cố định - câu đó nói thẳng là
 * tính năng chưa hoàn thiện, để buổi demo không tạo ấn tượng sai rằng bot đã
 * hiểu và tư vấn được.
 *
 * Khi làm chatbot thật: thay toàn bộ file này, và bỏ dòng include trong footer.tpl.
 *}
<div id="ttChatMock" class="tt-chat" aria-live="polite">
	<button type="button" id="ttChatToggle" class="tt-chat__bubble" aria-expanded="false" aria-controls="ttChatPanel" aria-label="Mở khung chat tư vấn">
		<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false">
			<path fill="currentColor" d="M12 3C6.98 3 3 6.58 3 11c0 2.4 1.2 4.53 3.1 5.96L5.4 20.4a.6.6 0 0 0 .87.66l3.9-2.05c.58.11 1.2.17 1.83.17 5.02 0 9-3.58 9-8s-3.98-8-9-8Z"/>
		</svg>
		<span class="tt-chat__dot" aria-hidden="true"></span>
	</button>

	<section id="ttChatPanel" class="tt-chat__panel" role="dialog" aria-modal="false" aria-labelledby="ttChatTitle" hidden>
		<header class="tt-chat__header">
			{* $logo_url theo đúng cấu hình PS_LOGO, giống header. Không dùng $img_dir:
			   biến đó trỏ vào thư mục ảnh của theme, không phải /img/ ở gốc. *}
			<div class="tt-chat__headtext">
				{* Logo đã có sẵn tên khách sạn nên không lặp lại bằng chữ. Tên vẫn nằm
				   ở alt và aria-label để trình đọc màn hình đọc được. *}
				<img src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}" class="tt-chat__logo"/>
				<p id="ttChatTitle" class="sr-only">{$shop_name|escape:'html':'UTF-8'}</p>
				<p class="tt-chat__status"><span class="tt-chat__online" aria-hidden="true"></span>Tư vấn trực tuyến</p>
			</div>
			<button type="button" id="ttChatClose" class="tt-chat__close" aria-label="Đóng khung chat">&times;</button>
		</header>

		<div id="ttChatLog" class="tt-chat__log">
			<div class="tt-chat__msg tt-chat__msg--bot">
				Xin chào, tôi là AI Chatbot tư vấn 24/7. Tôi có thể giúp gì cho quý khách?
			</div>
		</div>

		<form id="ttChatForm" class="tt-chat__form" autocomplete="off">
			<label class="sr-only" for="ttChatInput">Nội dung tin nhắn</label>
			<input type="text" id="ttChatInput" class="tt-chat__input" placeholder="Nhập câu hỏi của quý khách..." maxlength="300"/>
			<button type="submit" class="tt-chat__send" aria-label="Gửi tin nhắn">
				<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false">
					<path fill="currentColor" d="M3.2 20.6 21.5 12 3.2 3.4 3.2 10l12 2-12 2z"/>
				</svg>
			</button>
		</form>
	</section>
</div>

{literal}
<style>
.tt-chat{position:fixed;right:20px;bottom:20px;z-index:9999;font-family:inherit}
.tt-chat__bubble{position:relative;width:58px;height:58px;border:0;border-radius:50%;background:#1b2a41;color:#fff;
	box-shadow:0 6px 18px rgba(0,0,0,.28);cursor:pointer;display:flex;align-items:center;justify-content:center;
	transition:transform .18s ease,background .18s ease}
.tt-chat__bubble:hover{background:#24374f;transform:translateY(-2px)}
.tt-chat__bubble:focus-visible{outline:3px solid #bf9a4f;outline-offset:3px}
.tt-chat__dot{position:absolute;top:6px;right:6px;width:11px;height:11px;border-radius:50%;background:#bf9a4f;border:2px solid #1b2a41}
.tt-chat__panel{position:absolute;right:0;bottom:72px;width:330px;max-width:calc(100vw - 32px);
	background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 12px 36px rgba(0,0,0,.24);display:flex;flex-direction:column}
.tt-chat__header{display:flex;align-items:center;gap:10px;padding:12px 14px;background:#1b2a41;color:#fff}
/* Logo là wordmark ngang (420x110). Nhồi vào ô vuông + cover sẽ cắt mất hình
   toà nhà và chữ, nên giữ nguyên tỉ lệ bằng contain. */
/* Wordmark ngang 420x110: khoá chiều cao, chiều rộng tự theo tỉ lệ (~110px), và
   cho phép co lại trên khung hẹp mà không méo nhờ object-fit. */
.tt-chat__logo{display:block;height:30px;width:110px;max-width:100%;object-fit:contain;object-position:left center;
	background:#fff;border-radius:5px;padding:3px 5px}
.tt-chat__headtext{flex:1 1 auto;min-width:0}
.tt-chat__status{margin:5px 0 0;font-size:12px;color:#c8d2e0;display:flex;align-items:center;gap:5px}
.tt-chat__online{width:7px;height:7px;border-radius:50%;background:#5cd68a;display:inline-block}
.tt-chat__close{flex:0 0 auto;background:none;border:0;color:#c8d2e0;font-size:24px;line-height:1;cursor:pointer;padding:0 2px}
.tt-chat__close:hover{color:#fff}
.tt-chat__log{padding:14px;height:250px;overflow-y:auto;background:#f5f6f8;display:flex;flex-direction:column;gap:9px}
.tt-chat__msg{max-width:82%;padding:9px 12px;border-radius:12px;font-size:13.5px;line-height:1.45;word-wrap:break-word}
.tt-chat__msg--bot{background:#fff;color:#26313f;border:1px solid #e4e7ec;align-self:flex-start;border-bottom-left-radius:4px}
.tt-chat__msg--me{background:#1b2a41;color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.tt-chat__msg--note{background:#fdf6e6;border:1px solid #e8d9b4;color:#6b5a2e;font-size:12.5px;align-self:stretch;max-width:100%}
.tt-chat__form{display:flex;gap:8px;padding:10px;border-top:1px solid #e4e7ec;background:#fff}
.tt-chat__input{flex:1 1 auto;min-width:0;border:1px solid #d7dbe2;border-radius:20px;padding:9px 14px;font-size:13.5px;color:#26313f}
.tt-chat__input:focus{outline:0;border-color:#1b2a41}
.tt-chat__send{flex:0 0 auto;width:38px;height:38px;border:0;border-radius:50%;background:#bf9a4f;color:#fff;cursor:pointer;
	display:flex;align-items:center;justify-content:center}
.tt-chat__send:hover{background:#a9873f}
.tt-chat .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);border:0}
@media (max-width:530px){
	.tt-chat{right:14px;bottom:14px}
	.tt-chat__panel{width:calc(100vw - 28px)}
	.tt-chat__log{height:210px}
}
</style>

<script>
(function () {
	var root = document.getElementById('ttChatMock');
	if (!root) { return; }

	var toggle = document.getElementById('ttChatToggle');
	var panel  = document.getElementById('ttChatPanel');
	var closeB = document.getElementById('ttChatClose');
	var form   = document.getElementById('ttChatForm');
	var input  = document.getElementById('ttChatInput');
	var log    = document.getElementById('ttChatLog');
	var answered = false;

	function open() {
		panel.hidden = false;
		toggle.setAttribute('aria-expanded', 'true');
		input.focus();
	}
	function close() {
		panel.hidden = true;
		toggle.setAttribute('aria-expanded', 'false');
		toggle.focus();
	}
	function say(text, kind) {
		var el = document.createElement('div');
		el.className = 'tt-chat__msg tt-chat__msg--' + kind;
		el.textContent = text;
		log.appendChild(el);
		log.scrollTop = log.scrollHeight;
	}

	toggle.addEventListener('click', function () {
		if (panel.hidden) { open(); } else { close(); }
	});
	closeB.addEventListener('click', close);
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && !panel.hidden) { close(); }
	});

	form.addEventListener('submit', function (e) {
		e.preventDefault();
		var text = input.value.trim();
		if (!text) { return; }
		say(text, 'me');
		input.value = '';

		// Không gọi API, không có bot thật. Trả lời cố định và nói rõ như vậy,
		// để người xem demo không hiểu nhầm là máy đã trả lời được.
		window.setTimeout(function () {
			if (!answered) {
				say('Cảm ơn quý khách đã nhắn tin. Tính năng trả lời tự động đang trong quá trình hoàn thiện, lễ tân sẽ liên hệ lại trong thời gian sớm nhất.', 'bot');
				say('Giao diện demo - chưa đấu nối AI Chatbot.', 'note');
				answered = true;
			} else {
				say('Lễ tân đã nhận được tin nhắn của quý khách.', 'bot');
			}
		}, 450);
	});
})();
</script>
{/literal}
