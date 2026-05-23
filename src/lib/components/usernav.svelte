<script>
	import { page } from '$app/stores';
	import { userNavLinks } from '$lib/providers/urls';
	import CONTACTS from '$lib/providers/contacts';
	import { createEventDispatcher } from 'svelte';

	const dispatch=createEventDispatcher();

	const navigate=()=>{
		dispatch("navigate");
	}
</script>

<nav class="navbar">
	<div class="content">
		<ul class="nav-links block">
			{#each userNavLinks as item}
				<li>
					<a onclick={navigate} href={item.path} class={$page.url.pathname === item.path ? 'active flex gap-2' : 'flex gap-2'}>
						<div class="">
                            {@html item.icon}
                        </div>
                        <div class="">
                            {item.name}
                        </div>
					</a>
				</li>
			{/each}
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
		padding: 0.1rem .1rem;
		border-radius: 5px;
		transition: all 0.3s ease;
	}


	.nav-links a:hover {
		color: #7aea66;
		background: rgba(102, 234, 205, 0.1);
	}

	.nav-links a.active {
		color: #66ea9f;
		background: rgba(102, 234, 157, 0.1);
	}
</style>
