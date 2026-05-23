<script>
	import Quicklinks from "$lib/components/quicklinks.svelte";
	import Statsboard from "$lib/components/statsboard.svelte";
	import Title from "$lib/components/title.svelte";

</script>
<main class="pl-2">
    <div class="">
    <Title title="Dashboard" subtitle="At a glance"/>
    </div>

    <div class="my-2">
        <Quicklinks/>
    </div>

    <div class="">
        <Statsboard/>
    </div>
</main>