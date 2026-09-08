<div class="bootstrap">
	<nav id="{if $employee->bo_menu}nav-sidebar{else}nav-topbar{/if}" role="navigation">
		{if !$tab}
			<div class="mainsubtablist" style="display:none;"></div>
		{/if}
		<ul class="menu">
			<li class="searchtab">
				{include file="search_form.tpl" id="header_search" show_clear_btn=1}
			</li>
			{foreach $tabs as $t}
				{if $t.active}
				<li class="maintab {if $t.current}active{/if} {if $t.sub_tabs|@count}has_submenu{/if}" id="maintab-{$t.class_name}" data-submenu="{$t.id_tab}">
					<a href="{if $t.sub_tabs|@count && isset($t.sub_tabs[0].href)}{$t.sub_tabs[0].href|escape:'html':'UTF-8'}{else}{$t.href|escape:'html':'UTF-8'}{/if}" class="title" >
						<i class="icon-{$t.class_name}"></i>
						<span>{if $t.name eq ''}{$t.class_name|escape:'html':'UTF-8'}{else}{$t.name|escape:'html':'UTF-8'}{/if}</span>
					</a>
					{if $t.sub_tabs|@count}
						<ul class="submenu">
						{foreach from=$t.sub_tabs item=t2}
							{if $t2.active}
							<li id="subtab-{$t2.class_name|escape:'html':'UTF-8'}" {if $t2.current} class="active"{/if}>
								<a href="{$t2.href|escape:'html':'UTF-8'}">
									{if $t2.name eq ''}{$t2.class_name|escape:'html':'UTF-8'}{else}{$t2.name|escape:'html':'UTF-8'}{/if}
								</a>
							</li>
							{/if}
						{/foreach}
						</ul>
					{/if}
				</li>
				{/if}
			{/foreach}
		</ul>
		{*
		  Nút thu gọn menu. Bản gốc dùng icon-align-justify xoay 90 độ, ra bốn
		  vạch dọc "||||" - một hình không nói lên nó làm gì, lại không có
		  tooltip. Đổi sang mũi tên kép có hướng và thêm nhãn.

		  Lưu ý: admin-theme.js toggle class icon-rotate-90 lên chính thẻ span
		  này để ĐÁNH DẤU TRẠNG THÁI đang thu gọn, chứ không nhằm xoay hình -
		  icon cũ đối xứng nên xoay hay không trông vẫn thế. Vì vậy không đụng
		  vào JS; chỉ trung hoà phép xoay đó bằng CSS bên dưới rồi lật mũi tên
		  cho đúng chiều.
		*}
		<style>
			.menu-collapse.icon-rotate-90 { transform: none !important; }
			.menu-collapse > i { transition: transform .15s ease; }
			.menu-collapse.icon-rotate-90 > i { transform: rotate(180deg); }
		</style>
		<span class="menu-collapse" title="{l s='Thu gọn / mở rộng menu'}" aria-label="{l s='Thu gọn / mở rộng menu'}">
			<i class="icon-angle-double-left"></i>
		</span>
		{hook h='displayAdminNavBarBeforeEnd'}
	</nav>
</div>
