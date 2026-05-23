<script>
	import { logout } from '$lib/methods/methods';
    import pkg from 'notiflix';
	import { onMount } from 'svelte';

    const { Notify, Confirm } = pkg;

    onMount(()=>{
 
        logout();
    })

</script>