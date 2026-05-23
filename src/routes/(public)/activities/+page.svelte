<script>
	import Pageslider from "$lib/components/pageslider.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { get_activities } from "$lib/methods/methods";
	import { onMount } from "svelte";

    let title="Activities";

    let tagline="Get Involved With Us";

    let activities=[];

    onMount(async()=>{

        activities=await get_activities();

        console.log(activities);
    })
</script>

<main>
    <!-- slider  -->
    <div class="">
        <Pageslider {title} {tagline}/>
    </div>
    <!-- slider  -->
     <br>
    <div class="content my-4 p-5">
        <div class="">
            <div class="title text-2xl text-center font-bold mt-2">
                 <span class="text-wekebio-red">Our</span> <span class="text-wekebio-pastel">Activities</span>
                </div>
                <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
                    <br>
                </div>

        </div>
        <div class="activities-grid">
            {#each activities as activity}
                <div class="activity-card flex flex-col items-center shadow-lg cursor-pointer gap-4">
                    <div class="activity-img" style="background-image: url({API_BASE_URL}{activity.featured_image});">
                        
                    </div>
                <div class="font-bold">
                        {activity.title}
                </div>
                    <div class="">
                        {activity.description}
                    </div>

                    <div class="flex items-stretch justify-between w-full">
                        <div class="">
                            <i class="marker icon"></i> Venue: {activity.venue}
                        </div>

                        <div class="">
                        <i class="clock icon"></i>   Duration: {activity.duration}
                        </div>
                    </div>
                </div>
            {/each}
        </div>
    </div>

</main>


<style>

    .content{
        background: rgba(255, 255, 255, 0.15);
    }
    
    .activities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .activity-card {
        background: rgba(255, 255, 255, 1);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .activity-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }
    
    .activity-img{
        min-height: 10em;
        width: 100%;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }
    
    @media only screen and (min-width:768px){
            .activity-img{
        min-height: 15em;
        width: 100%;
    }
    
    }
</style>