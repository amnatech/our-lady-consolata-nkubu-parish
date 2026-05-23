<script>
	import Segment from "$lib/components/segment.svelte";
	import Title from "$lib/components/title.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource, post_resource } from "$lib/methods/functions";
	import { current_liu, get_houses } from "$lib/methods/methods";
	import { onMount } from "svelte";
	import { v4 } from "uuid";
    import pkg from 'notiflix';
	import { goto } from "$app/navigation";
    
    const { Notify, Confirm } = pkg;
    

    const URL=`${API_BASE_URL}groups.php`;

    const CREATE_URL=`${API_BASE_URL}create-group.php`;

    const RESOURCE="Groups";

    const liu=current_liu();

    let title="New Group";

    let subtitle="Create Group";

    let groupTitle;

    let groupPurpose;

    let description;

    let prayerHouse;

    let groupImages;

    let prayerHouses=[];

    let creating=false;


    const create=async()=>{
        try {

            creating=true;

            let fd=new FormData();

            fd.append("group_id",v4());

            fd.append("title",groupTitle);

            fd.append("purpose",groupPurpose);

            fd.append("prayer_house",prayerHouse);

            fd.append("description",description);

            fd.append("created_by",liu.name);


            for (let i = 0; i < groupImages.length; i++) {
                const image = groupImages[i];

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

        prayerHouses=await get_houses();

        console.log(prayerHouses);
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
                        <input type="text" name="title" id="title" bind:value={groupTitle} placeholder="group Title" required>
                    </div>

        
                </div>

                <div class="flex gap 2">
                    <div class="field required">
                        <label for="aim">Aim/Purpose</label> <br>
                        <input type="text" name="aim" id="aim" bind:value={groupPurpose} placeholder="Group Purpose" required>
                    </div>
                </div>


                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required">
                        <label for="subtitle">Prayer House</label> <br>

                        <select name="duration" id="duration" bind:value={prayerHouse} required>
                            <option value="">Select Prayer House</option>
                            {#each prayerHouses as house}
                                <option value="{house.name}">{house.name}</option>
                            {/each}
                        </select>
                    </div>
                </div>

                <div class="my-3">
                    <div class="field required ">
                        <label for="images">group Images</label> <br>
                                    
                        <input type="file" name="images" id="images" bind:files={groupImages} multiple accept=".jpeg,.png,.webp,.jpg"  required>
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