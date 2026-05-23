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

    const CREATE_URL=`${API_BASE_URL}create-house.php`;


    const RESOURCE="Activities";

    const liu=current_liu();

    let title="New Prayer House";

    let subtitle="Create Prayer House";

    let houseName;

    let houseEmail;

    let housePhone;

    let houseLocation;

    let houseImages;

    let creating=false;

    const create=async()=>{
        try {

            creating=true;

            let fd=new FormData();

            fd.append("house_id",v4());

            fd.append("name",houseName);

            fd.append("email",houseEmail);

            fd.append("phone",housePhone);

            fd.append("location",houseLocation);

            fd.append("created_by",liu.name);


            for (let i = 0; i < houseImages.length; i++) {
                const image = houseImages[i];

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

                <div class="my-3">
                    <div class="field required ">
                        <label for="title">Name</label> <br>
                        <input type="text" name="title" id="title" bind:value={houseName} placeholder="Prayer House Name" required>
                    </div>

                </div>

                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="title">Phone</label> <br>
                        <input type="tel" name="phone" id="title" bind:value={housePhone} placeholder="Phone" required>
                    </div>

                    <div class="field required">
                        <label for="subtitle">Subtitle</label> <br>
                        <input type="text" name="subtitle" id="subtitle" bind:value={houseEmail} placeholder="Email" required>
                    </div>
                </div>



                <div class="flex gap-2 flex-col md:flex-row my-3">

                    <div class="field required ">
                        <label for="houseLocation">Location</label> <br>
                        <input type="text" name="houseLocation" id="houseLocation" bind:value={houseLocation} placeholder="houseLocation" required>
                    </div>

            
                </div>

                <div class="my-3">
                    <div class="field required ">
                        <label for="images">House Images</label> <br>
                                    
                        <input type="file" name="images" id="images" bind:files={houseImages} multiple accept=".jpeg,.png,.webp,.jpg"  required>
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



    label{
        padding-bottom: 1em;
    }
</style>