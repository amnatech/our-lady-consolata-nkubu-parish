<script>
	export let title;

	export let subtitle;
</script>

<div class="nav">
	<div
		class="flex justify-between items-center page_title_bar p-2 border border-t-0 border-l-0 border-r-0 border-b-pp_border"
	>
		<div class="">
			<div class="title text-2xl capitalize">
				{title}
			</div>
			<div class="description text-gray-700 dark:text-slate-500 text-base font-light pt-1 capitalize">
				{subtitle}
			</div>
		</div>
		<div class="">
			<slot name="action" />
		</div>
	</div>
</div>
