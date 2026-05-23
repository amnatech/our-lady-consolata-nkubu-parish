<script>
	import Title from '$lib/components/title.svelte';

let title="Tools";

let subtitle="All Modules In One Place";
	
let { children } = $props();

</script>

<div class="">
            <Title {title} {subtitle}>
            <div class="" slot="action">
               
            </div>
        </Title>
</div>

{@render children()}
