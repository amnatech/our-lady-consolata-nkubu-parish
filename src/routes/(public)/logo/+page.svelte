<script>
	import Logobar from "$lib/components/logobar.svelte";
	import Segment from "$lib/components/segment.svelte";

</script>
<main>
    <div class="my-2 p-2">
        <Segment>
            <div class="flex justify-center items-center" slot="content">
                <Logobar/>
            </div>
        </Segment>
    </div>
</main>