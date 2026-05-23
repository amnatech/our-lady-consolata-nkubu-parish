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
    

    const URL=`${API_BASE_URL}activities.php`;

    const CREATE_URL=`${API_BASE_URL}create-activity.php`;


    const RESOURCE="Activities";

    const liu=current_liu();

    let title="New Activity";

    let subtitle="Create Activity";

    let activityTitle;

    let activitySubtitle;

    let activityDuration;

    let description;

    let venue;

    let activityImages;

    let creating=false;

    let durations=["full day","half day","afternoon","weekend","week","flexible","other"];



    const create=async()=>{
        try {

            creating=true;

            let fd=new FormData();

            fd.append("activity_id",v4());

            fd.append("title",activityTitle);

            fd.append("subtitle",activitySubtitle);

            fd.append("duration",activityDuration);

            fd.append("venue",venue);

            fd.append("description",description);

            fd.append("created_by",liu.name);


            for (let i = 0; i < activityImages.length; i++) {
                const image = activityImages[i];

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

                goto('/user/activities');
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
                <a href="/user/activities">
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
                        <input type="text" name="title" id="title" bind:value={activityTitle} placeholder="Activity Title" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">Subtitle</label> <br>
                        <input type="text" name="subtitle" id="subtitle" bind:value={activitySubtitle} placeholder="Activity Subtitle" required>
                    </div>
                </div>



                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="venue">Venue</label> <br>
                        <input type="text" name="venue" id="venue" bind:value={venue} placeholder="Venue" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">Duration</label> <br>

                        <select name="duration" id="duration" bind:value={activityDuration} required>
                            <option value="">Select Duration</option>
                            {#each durations as duration}
                                <option value="{duration}">{duration}</option>
                            {/each}
                        </select>
                    </div>
                </div>

                <div class="my-3">
                    <div class="field required ">
                        <label for="images">Activity Images</label> <br>
                                    
                        <input type="file" name="images" id="images" bind:files={activityImages} multiple accept=".jpeg,.png,.webp,.jpg"  required>
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

    select{
        width: 100%;

    }

    textarea{
        width: 100%;
    }

    label{
        padding-bottom: 1em;
    }
</style>