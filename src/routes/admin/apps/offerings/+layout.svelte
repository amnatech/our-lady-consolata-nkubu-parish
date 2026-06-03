<script>
	import Title from '$lib/components/title.svelte';

let title="Offerings Tracker";

let subtitle="View & Manage offering Contributions";
	
let { children } = $props();

</script>

<div class="">
            <Title {title} {subtitle}>
            <div class="" slot="action">
               
            </div>
        </Title>
</div>

{@render children()}
