<script>
	import '../layout.css';
	import favicon from '$lib/assets/favicon.svg';
	import { onMount } from 'svelte';
	import { current_liu } from '$lib/methods/methods';
	import { goto } from '$app/navigation';
	import Segment from '$lib/components/segment.svelte';
	import Usernav from '$lib/components/usernav.svelte';

	const liu=current_liu();

	let showUserNav=$state(false);


		const openUserNav=()=>{
		showUserNav=true;
	}

	const closeUserNav=()=>{
		showUserNav=false;
	}

	let { children } = $props();

	// check if logged in 
	onMount(async()=>{
	
	})
</script>

<svelte:head><link rel="icon" href={favicon} /></svelte:head>


<div class="content">
	<div class="flex flex-col md:flex-row">
		<div class="side-col flex-1 bg-amber-50 shadow">

			<Segment>
				<div class="capitalize" slot="title">
					Welcome Back {liu?.username},
				</div>

				<div class="" slot="actions">
					{#if showUserNav}
					<!-- svelte-ignore a11y_consider_explicit_label -->
					<button onclick={closeUserNav} class="font-bold text-xl ui icon button">
						                     <i  class="ri-close-large-line"></i>

					</button>
						{:else}
<!-- svelte-ignore a11y_consider_explicit_label -->
					<button onclick={openUserNav} class="font-bold text-xl ui icon button">
						<i class="ri-menu-4-line"></i>
					</button>
					{/if}
					
				</div>
			</Segment>

			<!-- mobile nav -->

			{#if showUserNav}
					<Segment>
				<div class="" slot="content">
					<div class="">
						<Usernav/>
					</div>
	
				</div>
			</Segment>
			{/if}

			<!-- mobile nav -->

			<div class="comp-nav hidden md:block">
				<Segment>
				<div class="" slot="content">
					<div class="">
						<Usernav/>
					</div>
	
				</div>
			</Segment>

			</div>

		
	
		</div>
		<div class="main-col flex-3">
			
{@render children()}
		</div>
	</div>
</div>




