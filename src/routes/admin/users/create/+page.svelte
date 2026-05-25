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
    

    const URL=`${API_BASE_URL}users.php`;

    const CREATE_URL=`${API_BASE_URL}create-user.php`;

    const RESOURCE="Users";

    const liu=current_liu();

    let title="New User";

    let subtitle="Create User";

    let firstName;

    let lastName;

    let email;

    let phone;

    let prayerHouse;

    let profilePicture;

    let prayerHouses=[];

    let creating=false;

    let userTitle;

    let userGroup;

    let gender;

    let titles=["Dr","Mr","Ms","Mrs","Phd","Other"];

    let userGroups=["admin","user","guest","group","prayer_house"];

    let genders=["male","female","other"];

    let dob;//date of birth


    const create=async()=>{
        try {

            creating=true;

            let fd=new FormData();

            fd.append("user_id",v4());

            fd.append("title",userTitle);

            fd.append("email",email);

            fd.append("phone",phone);

            fd.append("firstname",firstName);

            fd.append("lastname",lastName);

            fd.append("prayer_house",prayerHouse);

            fd.append("user_group",userGroup);

            fd.append("dob",dob);

            fd.append("gender",gender);

            fd.append("created_by",liu.name);


            for (let i = 0; i < profilePicture.length; i++) {
                const image = profilePicture[i];

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

                goto('/admin/users');
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
                <a href="/admin/users">
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
                        <select name="title" id="title" bind:value={userTitle} required>
                            <option value="">Select Title</option>
                            {#each titles as t}
                                <option value="{t}">{t}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="field required ">
                        <label for="firstname">First Name</label> <br>
                        <input type="text" name="firstname" id="firstname" bind:value={firstName} placeholder="First Name" required>
                    </div>

                    <div class="field required ">
                        <label for="lastname">Last Name</label> <br>
                        <input type="text" name="lastname" id="lastname" bind:value={lastName} placeholder="Last Name" required>
                    </div>

        
                </div>

                <div class="flex gap-2">
                    <div class="field required">
                        <label for="email">Email</label> <br>
                        <input type="email" name="email" id="email" bind:value={email} placeholder="abc@exmple.com" required>
                    </div>

                    <div class="field required">
                        <label for="phone">Phone</label> <br>
                        <input type="tel" name="phone" id="phone" bind:value={phone} placeholder="Phone No" required>
                    </div>
                </div>


                <div class="flex gap-2 flex-col md:flex-row my-3"> 
                    <div class="field required">
                        <label for="groups">User Groups</label> <br>

                        <select name="groups" id="groups" bind:value={userGroup} required>
                            <option value="">Select User Group</option>
                            {#each userGroups as group}
                                <option value="{group}">{group}</option>
                            {/each}
                        </select>
                    </div>

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

                    <div class="flex gap-2 flex-col md:flex-row my-3"> 
                    <div class="field required">
                        <label for="gender">Gender</label> <br>

                        <select name="gender" id="gender" bind:value={gender} required>
                            <option value="">Select Gender</option>
                            {#each genders as gender}
                                <option value="{gender}">{gender}</option>
                            {/each}
                        </select>
                    </div>

                    <div class="field required">
                        <label for="subtitle">D.O.B</label> <br>
                        <input type="date" name="dob" id="dob" bind:value={dob} placeholder="01/01/2000" required>

             
                    </div>
                </div>


                <div class="my-3">
                    <div class="field required ">
                        <label for="images">Profile Picture</label> <br>
                                    
                        <input type="file" name="images" id="images" bind:files={profilePicture} accept=".jpeg,.png,.webp,.jpg"  required>
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