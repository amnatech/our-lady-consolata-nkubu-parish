<script>
	import Corevalues from "$lib/components/corevalues.svelte";
	import Introduction from "$lib/components/introduction.svelte";
	import Missionvison from "$lib/components/missionvison.svelte";
	import Pageslider from "$lib/components/pageslider.svelte";
	import Whoweare from "$lib/components/whoweare.svelte";
    import { org_details } from '$lib/methods/methods';
    import { onMount } from 'svelte';

    let organisation;
    

    let title="About Us";

    let tagline="know who we are";

    onMount(async() => {
        // console.log('Home page loaded');
        organisation=await org_details();
    });

</script>

<main>

    <!-- slider  -->
    <div class="">
        <Pageslider {title} {tagline}/>
    </div>
    <!-- slider  -->

    {#if organisation}
        <!-- who we are  -->
         <div class="bg-white">
            <Whoweare {organisation}/>
         </div>
        <!-- who we are  -->

        <!-- mission vision  -->
        <div class="my-2">
            <Missionvison {organisation}/>
         </div>
        <!-- mission vision  -->

        <div class="content my-4">
            <div class=" my-4">
                <Corevalues values={organisation.detail.values}/>
            </div>

                    <div class="section">
               <div class="title text-2xl text-center font-bold mt-2">
                 <span class="text-wekebio-red">Our</span> <span class="text-consolata-blue">Goal</span>
                </div>
                <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
                    <br>
                </div>

            <p>
                {@html organisation?.detail.goal}
            </p>
        </div>
        
        <div class="section">
                       <div class="title text-2xl text-center font-bold mt-2">
                 <span class="text-consolata-blue">Our</span> <span class="text-wekebio-red">Team</span>
                </div>
                <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
                    <br>
                </div>
            <p>
                {@html organisation?.detail.team}
            </p>
        </div>

        </div>

  

    {/if}

</main>

<style>

    
    .section {
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        margin-top: 2rem;

    }
    
    p{
        color: #fff;
        text-align: center;
        line-height: 2.5em;
    }
    

</style>