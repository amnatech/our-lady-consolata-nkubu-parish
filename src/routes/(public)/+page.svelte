<script>
	import Calltoaction from '$lib/components/calltoaction.svelte';
	import Corevalues from '$lib/components/corevalues.svelte';
	import Introduction from '$lib/components/introduction.svelte';
	import Photogallery from '$lib/components/photogallery.svelte';
	import Slider from '$lib/components/slider.svelte';
	import { org_details } from '$lib/methods/methods';
    import { onMount } from 'svelte';

    let organisation;
    
    onMount(async() => {
        // console.log('Home page loaded');
        organisation=await org_details();
    });
</script>


<!-- silder  -->
 <div class="slider">
    <Slider/>
 </div>
<!-- silder  -->

<div class="bg-white">
    <!-- introduction  -->
    <div class="">
        {#if organisation}
            <Introduction {organisation}/>
        {/if}
    </div>
    <!-- introduction  -->

</div>

<div class="content flex flex-col gap-2 pt-5">
    <!-- cta  -->
     <div class="my-2 py-5">
        <Calltoaction/>
     </div>
    <!-- cta  -->

    <div class="my-2 py-5">
        {#if organisation}
            <Corevalues values={organisation.detail.values}/>
        {/if}
    </div>

    <div class="my-2 py-5">
        <Photogallery/>
    </div>
</div>

<style>
  
</style>