<script>
	import './layout.css';
	// import '$lib/styles/app.css';
	import favicon from '$lib/assets/logos/consolata-icon.png';

	import { page } from '$app/stores';
	import Footer from '$lib/components/footer.svelte';
	import { mainNavLinks } from '$lib/providers/urls';
	import Navbar from '$lib/components/navbar.svelte';
	import Headbar from '$lib/components/headbar.svelte';

	let { children } = $props();
</script>

<svelte:head><link rel="icon" href={favicon} /></svelte:head>

<header>
	<Headbar />
</header>

	{@render children()}


<footer>
	<Footer />
</footer>

<style>
	:global(body) {
		margin: 0;
		padding: 0;
		font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
		background: linear-gradient(135deg, #d6d8d7 0%, #2f7b99a7 100%);
		min-height: 100vh;
	}

</style>
