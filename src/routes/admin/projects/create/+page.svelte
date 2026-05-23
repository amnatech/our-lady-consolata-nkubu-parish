<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { current_liu } from "$lib/methods/methods";
	import { onMount } from "svelte";
	import { v4 } from "uuid";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
    
    const { Notify, Confirm } = pkg;
    

    const URL=`${API_BASE_URL}projects.php`;

    const CREATE_URL=`${API_BASE_URL}create-project.php`;


    const RESOURCE="Projects";

    const liu=current_liu();

    let title="New Project";

    let subtitle="Create Project";

    let projectTitle;

    let projectSubtitle;

    let projectYear;

    let description;

    let location;

    let projectImages;

    let creating=false;

    let startDate;

    let endDate;


    const create=async()=>{
        try {

            creating=true;

            let fd=new FormData();

            fd.append("project_id",v4());

            fd.append("title",projectTitle);

            fd.append("subtitle",projectSubtitle);

            fd.append("project_year",projectYear);

            fd.append("start_date",startDate);
            
            fd.append("end_date",endDate);


            fd.append("location",location);

            fd.append("description",description);

            fd.append("created_by",liu.name);


            for (let i = 0; i < projectImages.length; i++) {
                const image = projectImages[i];

                fd.append("images",image);
              
            }

            let headers={
                'Content-Type':'multipart/formdata'
            }

            const RESPONSE=await post_resource(RESOURCE,CREATE_URL,fd,headers);

            const RES=RESPONSE.data;

            // console.log(RES);

            if(RES.success){
                Notify.success(RES.message)

                goto('/user/projects');
            }else{
                Notify.failure(RES.message)
            }

            creating=false;


        } catch (err) {
            console.log(err)
        }
    }


    onMount(async()=>{

    })

</script>
<main>

    <div class="">
        <Title {title} {subtitle}>
            <div class="" slot="action">
               
            </div>
        </Title>
    </div>

    <div class="my-2 md:pl-2">
        <Segment>
            <div class="" slot="title">
                <a href="/user/projects">
                 <button class="ui mini icon purple basic button">
                    <i class="left arrow icon"></i> Back
                </button>
               </a>
            </div>
            <div class="" slot="actions">
  
            </div>
            <div class="m-2" slot="content">
                <form class="ui form" onsubmit={(e)=>{
                    e.preventDefault();

                    create();
                }}>

                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="title">Title</label> <br>
                        <input type="text" name="title" id="title" bind:value={projectTitle} placeholder="project Title" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">Subtitle</label> <br>
                        <input type="text" name="subtitle" id="subtitle" bind:value={projectSubtitle} placeholder="project Subtitle" required>
                    </div>
                </div>



                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="location">Location</label> <br>
                        <input type="text" name="location" id="location" bind:value={location} placeholder="Location" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">Year</label> <br>
                        <input type="number" name="year" id="year" bind:value={projectYear} placeholder="2026" min="2025" max="2035" required>
                    </div>
                </div>

                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="start">Start Date</label> <br>
                        <input type="date" name="start" id="start" bind:value={startDate} placeholder="00-00-0000" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">End Date</label> <br>
                        <input type="date" name="end" id="end" bind:value={endDate} placeholder="00-00-0000" required>
                    </div>
                </div>


                <div class="my-3">
                    <div class="field required ">
                        <label for="images">Project Images</label> <br>
                                    
                        <input type="file" name="images" id="images" bind:files={projectImages} multiple accept=".jpeg,.png,.webp,.jpg"  required>
                    </div>
                </div>

                <div class="my-3">
                     <div class="field required ">
                        <label for="description">Description</label> <br>
                        <textarea name="description" id="description" placeholder="Write Description Here ..." rows="5" bind:value={description}></textarea>
                    </div>
                </div>

                <div class="my-2 text-center p-4">
                    <button class={creating?"ui icon mini purple loading button":"ui icon mini purple button"}>
                        <i class="send icon"></i> Create
                    </button>
                </div>

                </form>
            </div>
        </Segment>
    </div>
</main>

<style>
    .field{
        flex:auto
    }

    input{
        width: 100%;
    }


    textarea{
        width: 100%;
    }

    label{
        padding-bottom: 1em;
    }
</style>