<script>
	import { onMount } from 'svelte';


</script>

<div
	class="segment overflow-hidden p-2 border border-pp_border bg-white rounded-md shadow-md text-gray-600 dark:text-gray-400"
>
	{#if $$slots.title}
		<div class="titlebar py-2 flex justify-between items-center border-b-2">
			<div class="text-lg">
				<slot name="title" />
			</div>

			{#if $$slots.actions}
				<div class="">
					<slot name="actions" />
				</div>
			{/if}
		</div>
	{/if}

	<div class="content bar">
		<slot name="content" />
	</div>
</div>

<style>
	.segment {
		max-width: 96vw;
		margin: auto;
	}
</style>
