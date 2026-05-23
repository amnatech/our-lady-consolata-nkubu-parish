<script>
	// @ts-nocheck

	import { add_commas } from '$lib/methods/methods';

	export let stat;
</script>

{#if stat}
	{#if stat.url}
		<a href="{stat.url}"
			class="flex justify-between p-2 items-center border-dotted border-2 border-pp_border w-[49%] md:w-[18%] bg-white shadow-md"
		>
			<div class="">
				<div
					class="bg-slate-200  w-10 h-10 rounded-md m-2 flex items-center justify-center font-bold text-lg text-slate-500"
				>
					<i class="ri-fullscreen-fill"> </i>
				</div>
			</div>
			<div class="py-2 flex-auto">
				<div class="">
					<div class="font-extrabold text-lg">
						{#if stat.type === 'currency'}
							<span class="text-sm font-light"> Ksh </span>

							<span>
								{add_commas(parseFloat(stat.value), 2)}
							</span>
						{:else}
							<span>
								{add_commas(parseFloat(stat.value))}
							</span>
						{/if}
					</div>
				</div>
				<div class="text-sm font-medium capitalize">
					{stat.name}
				</div>
			</div>

			<div class="text-sm font-medium">
				{#if stat.change}
					{#if stat.change > 1}
						<span class="text-green-400 flex"
							><i class="ri-arrow-up-line pl-2"></i> +{stat.change}%</span
						>
					{:else if stat.change < 0}
						<span class="text-red-400 flex"
							><i class="ri-arrow-down-line pl-2"></i>{stat.change}%</span
						>
					{:else}
						<span class="">{stat.change}%</span>
					{/if}
				{/if}
			</div>
		</a>
	{:else}
		<div
			class="flex justify-between p-2 items-center  border-pp_border w-[49%] md:w-[24%] bg-white hover:bg-slate-50 shadow"
		>
			<div class="">
				<div
					class="bg-slate-200 dark:bg-slate-600 w-10 h-10 rounded-md m-2 flex items-center justify-center font-bold text-lg text-slate-500"
				>
					<i class="ri-fullscreen-fill"> </i>
				</div>
			</div>
			<div class="py-2 flex-auto">
				<div class="">
					<div class="font-extrabold text-lg">
						{#if stat.type === 'currency'}
							<span class="text-sm font-light"> Ksh </span>

							<span>
								{add_commas(parseFloat(stat.value), 2)}
							</span>
						{:else}
							<span>
								{add_commas(parseFloat(stat.value))}
							</span>
						{/if}
					</div>
				</div>
				<div class="text-sm font-medium capitalize">
					{stat.name}
				</div>
			</div>

			<div class="text-sm font-medium">
				{#if stat.change}
					{#if stat.change > 1}
						<span class="text-green-400 flex"
							><i class="ri-arrow-up-line pl-2"></i> +{stat.change}%</span
						>
					{:else if stat.change < 0}
						<span class="text-red-400 flex"
							><i class="ri-arrow-down-line pl-2"></i>{stat.change}%</span
						>
					{:else}
						<span class="">{stat.change}%</span>
					{/if}
				{/if}
			</div>
		</div>
	{/if}
{/if}
