<script>
	import Pageslider from "$lib/components/pageslider.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { get_projects } from "$lib/methods/methods";
	import { onMount } from "svelte";

    let title="Projects";

    let tagline="Explore Our Projects";

    let projects=[];

    onMount(async()=>{

        projects=await get_projects();

        console.log(projects);
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
        <div class="py-2">
            <div class="title text-2xl text-center font-bold m-2">
                 <span class="text-wekebio-red">Our </span> <span class="text-wekebio-pastel"> Projects</span>
                </div>
                <div class="hline border-t-6 w-18 m-auto border-wekebio-purple mb-2">
                    <br>
                </div>

        </div>
        <div class="projects-grid">
            {#each projects as project}
                <div class="project-card flex flex-col items-center shadow-lg cursor-pointer gap-4">
                    <div class="project-img" style="background-image: url({API_BASE_URL}{project.featured_image});">
                        
                    </div>
                <div class="font-bold">
                        {project.title}
                </div>
                    <div class="">
                        {project.description}
                    </div>

                    <div class="flex items-stretch justify-between w-full text-sm">
                        <div class="">
                            <i class="marker icon"></i>Locn. {project.location}
                        </div>

                        <div class="">
                        <i class="calendar icon"></i>Year. {project.project_year}
                        </div>
                    </div>

                    <div class="flex items-stretch justify-between w-full text-sm">
                        <div class="">
                            <i class="calendar icon"></i>Start. {project.start_date}
                        </div>

                        <div class="">
                        <i class="calendar icon"></i>Compl.  {project.end_date}
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
    
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .project-card {
        background: rgba(255, 255, 255, 1);
        backdrop-filter: blur(10px);
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .project-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }
    
    .project-img{
        min-height: 10em;
        width: 100%;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }
    
    @media only screen and (min-width:768px){
            .project-img{
        min-height: 15em;
        width: 100%;
    }
    
    }
</style>