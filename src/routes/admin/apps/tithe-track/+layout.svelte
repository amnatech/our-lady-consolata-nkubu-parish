<script>
	import Title from '$lib/components/title.svelte';

let title="Tithe Tracker";

let subtitle="View & Manage Tithe Contributions";
	
let { children } = $props();

</script>

<div class="">
            <Title {title} {subtitle}>
            <div class="" slot="action">
               
            </div>
        </Title>
</div>

{@render children()}
