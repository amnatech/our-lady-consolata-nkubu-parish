<script>
	import { onMount } from "svelte";
	import Segment from "./segment.svelte";
	import { get_users } from "$lib/methods/methods";
    import pkg from 'notiflix';
    import { createEventDispatcher } from "svelte";

    const dispatch= createEventDispatcher();;

    const { Notify, Confirm } = pkg;

    let users=[];

    let filteredUsers=[];

    let searchQuery;


    const close_search_user=()=>{
        Confirm.show("Close Search User",
        "Do You Want To Exit Tithe Addition User Search?",
        "Yes","No",
        ()=>{dispatch("close")},
        ()=>{})
    }
    const search=(q)=>{
        const filtered=users.filter((u)=>{
            if(u.firstname.toLowerCase().includes(q) || u.lastname.toLowerCase().includes(q)){
                return u
            }
        })

        filteredUsers=filtered;

        console.log(filteredUsers);
    }

    onMount(async()=>{
        users=await get_users();

        console.log(users);
    })

</script>

<main class="fixed top-0 left-0 w-[100vw] h-[100vh] z-1000 p-5">
<div class="content m-4 p-4">
    <Segment>
        <div class="" slot="title">
            <i class="search icon"></i> Search User
        </div>

        <div class="" slot="actions">
            <div class="text-3xl">
                <!-- svelte-ignore a11y_consider_explicit_label -->
                <button onclick={close_search_user}>
                    <i class="ri-close-circle-line cursor-pointer text-red-600"></i>
                </button>
            </div>
        </div>
        <div class="my-4" slot="content">
            <div class="ui input ">
                <input class="w-full h-15" type="search" placeholder="Search Here .." bind:value={searchQuery} onkeyup={(e)=>{

                    let q=e.target.value;

                    if(q.length<2){
                        filteredUsers=[];
                        return
                    }

                    search(q);
                }}>
            </div>

            <div class="py-3">

                <table class="ui unstackable striped table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th><i class="user icon"></i> Name</th>
                            <th><i class="mail icon"></i> Email</th>
                            <th> <i class="phone icon"></i> Phone</th>
                            <th><i class="ri-cross-line"></i> Prayer House</th>
                            <th>Select</th>
                        </tr>
                    </thead>

                    <tbody>
                        {#each filteredUsers as u,i }
                        
                            <tr class="cursor-pointer hover:bg-slate-100" onclick={()=>{
                                Confirm.show(
                                    "Select User",
                                    `Select User ${u.firstname} ${u.lastname}?`,
                                    "Yes",
                                    "No",
                                    ()=>{
                                        // console.log(u)
                                        dispatch("search",u);
                                    }
                                )
                                }}>

                                <td>{i+1}</td>
                                <td>
                                    {u.title} {u.firstname} {u.lastname}
                                </td>
                                <td>
                                    {u.email}

                                </td>
                                <td>
                                    {u.phone}
                                </td>
                                <td>
                                    {u.extra_details?.prayer_house}
                                </td>
                                <td>
                                    <span class="underline text-consolata-blue">
                                        Select
                                    </span>
                                </td>
                            </tr>
                        {/each}
                    </tbody>
                </table>


                {#each filteredUsers as u,i}
                    <!-- svelte-ignore a11y_click_events_have_key_events -->
                    <!-- svelte-ignore a11y_no_static_element_interactions -->
                    <div onclick={()=>{
                        Confirm.show(
                            "Select User",
                            `Select User ${u.firstname} ${u.lastname}?`,
                            "Yes",
                            "No",
                            ()=>{
                                // console.log(u)
                                dispatch("search",u);
                            }
                        )
                    }} class="cursor-pointer flex gap-4 my-2 p-2 b-1 hover:bg-slate-200 {i%2==0?"bg-slate-100":"bg-white"}">
                        <div class="">
                            
                        </div>
                        <div class="">
                        </div>
                        <div class="">
                            
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </Segment>
</div>

</main>

<style>
    main{
        background: rgba(0, 0, 0, 0.377);
        backdrop-filter: blur(2px);
    }
</style>