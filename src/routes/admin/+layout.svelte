<script>
	import '../layout.css';
	import favicon from '$lib/assets/favicon.svg';
	import { onMount } from 'svelte';
	import { current_liu } from '$lib/methods/methods';
	import { goto } from '$app/navigation';
	import Segment from '$lib/components/segment.svelte';
	import Adminnav from '$lib/components/adminnav.svelte';
	import AdminSidebar from '$lib/components/admin-sidebar.svelte';
	import { page } from '$app/stores';



	const liu=current_liu();

	let showSideBar=$state(false);

	let showUserNav=$state(false);

	let currentUrl=$page.url.pathname;

	const openUserNav=()=>{
		showUserNav=true;
	}

	const closeUserNav=()=>{
		showUserNav=false;
	}


	$effect(()=>{
		// console.log(currentUrl);
		if(currentUrl!=$page.url.pathname){
			console.log($page.url.pathname);
			showSideBar=false;
		}

	})
	


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
						<Adminnav on:navigate={closeUserNav}/>
					</div>
	
				</div>
			</Segment>
			{/if}

			<!-- mobile nav -->

			<div class="comp-nav hidden md:block">
				<Segment>
				<div class="" slot="content">
					<div class="">
						<Adminnav/>
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


<div class="cta_bar fixed z-10000 bottom-0 right-2 m-5 cursor">
	<!-- svelte-ignore a11y_click_events_have_key_events -->
	<!-- svelte-ignore a11y_no_static_element_interactions -->
	<div onclick={()=>{
		showSideBar=true;
	}} class="cta-btn text-4xl bg-wekebio-red text-white text-center p-5 shadow-lg cursor-pointer hover:bg-red-700">
		<i class="ui cog icon"></i>
	</div>
</div>


<!-- side nav  -->
{#if showSideBar}
	<AdminSidebar on:close={()=>{
		showSideBar=false;
	}}/>
{/if}
<!-- side nav  -->


<style>
	.cta-btn{
		border-radius: 50%;
	}
</style>
