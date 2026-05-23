<script>
	import { page } from '$app/stores';
	import { mainNavLinks } from '$lib/providers/urls';
	import CONTACTS from '$lib/providers/contacts';
	import { createEventDispatcher } from 'svelte';
	import { current_liu } from '$lib/methods/methods';

	const liu=current_liu();

	const dispatch=createEventDispatcher();

	const navigate=()=>{
		dispatch("navigate");
	}

	const update_link=(link)=>{
		if(link=='Login' && liu){
			return liu.username
		}else{
			return link;
		}
	}
</script>

<nav class="navbar">
	<div class="content">
		<ul class="nav-links block md:flex">
			{#each mainNavLinks as item}
				<li>
					<a onclick={navigate} href={item.path} class={$page.url.pathname === item.path ? 'active capitalize' : 'capitalize'}>
							{update_link(item.name)}
					</a>
			
				</li>
			{/each}

			<li></li>
			<li></li>

			<li class=" flex-1 text-left md:text-right">
					<button class="text-wekebio-red font-bold">
						Donate

					</button>
					
			</li>
		</ul>
	</div>
</nav>

<style>
	.navbar {
		background: rgba(255, 255, 255, 0.95);
		backdrop-filter: blur(10px);
		box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
		position: sticky;
		top: 0;
		z-index: 1000;
	}

	.navbar .content {
		/* display: flex;
		justify-content: space-between;
		align-items: center; */
		padding: .3rem 10px;
	}
	.nav-links {
		/* display: flex; */
		gap: 2rem;
		list-style: none;
		margin: 0;
		padding: 0;
		line-height: 2.5em;
	}

	.nav-links a {
		text-decoration: none;
		color: #555;
		font-weight: 500;
		padding: 0.5rem 1rem;
		border-radius: 5px;
		transition: all 0.3s ease;
	}

	button
	{
		padding-left: 1em;

	}

	.nav-links a:hover {
		color: #069ae4;
		background: rgba(102, 197, 234, 0.1);
	}

	.nav-links a.active {
		color: #0284c7;
		background: rgba(102, 203, 234, 0.1);
	}
</style>
