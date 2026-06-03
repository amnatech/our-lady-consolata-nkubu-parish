<script>
	import { onMount } from "svelte";
	import Segment from "./segment.svelte";
	import { current_liu, get_houses, get_users } from "$lib/methods/methods";
    import pkg from 'notiflix';
    import { createEventDispatcher } from "svelte";
	import { v4 } from "uuid";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { post_resource } from "$lib/methods/functions";

    const RESOURCE="Tithes";

    const TITHE_URL=`${API_BASE_URL}tithes.php`;

    const dispatch= createEventDispatcher();;

    const { Notify, Confirm } = pkg;

    const liu=current_liu();


    export let user;

    export let serial;

    let loading=false;

    let titheAmount;

    let name;

    let prayerHouse

    let titheMonth;

    let serialNo;

    let transactionRef;

    let prayerHouses=[];


    const create_tithe=async()=>{

        let dt={
            tithe_id:v4(),
            user_id:user.user_id,
            name:name,
            house:prayerHouse,
            month:titheMonth,
            amount:titheAmount,
            transaction_ref:transactionRef?transactionRef:"",
            serial_no:serialNo,
            created_by:liu.name
        }

        try {
            loading=true;

                    
            let headers={
                "Content-Type": "application/x-www-form-urlencoded",
            }

            const RESPONSE=await post_resource(RESOURCE,TITHE_URL,dt,headers);

            const RES=RESPONSE.data;

            if(RES.success){
                Notify.success("Tithe Added Successfully");

                dispatch("close");
            }else{
                Notify.failure(RES.message);
            }

            loading=false;
        } catch (err) {
            console.log(err)
        }


    }


    const close_add_tithe=()=>{
        Confirm.show("Close Tithe Addition",
        "Do You Want To Exit Tithe Addition?",
        "Yes","No",
        ()=>{dispatch("close")},
        ()=>{})
    }

    const update_tithe_form=(user)=>{
        name=  `${user.title} ${user.firstname} ${user.lastname}`;

        if(user.extra_details){
            prayerHouse=user.extra_details?.prayer_house
        }
    }

    onMount(async()=>{

        prayerHouses=await get_houses();

        serialNo=serial;

        update_tithe_form(user);

  
    })

</script>

<main class="fixed top-0 left-0 w-[100vw] h-[100vh] z-1000 p-5">
<div class="content m-4 p-4">
    <Segment>
        <div class="" slot="title">
            <i class="plus icon"></i> Add Tithe
        </div>
        
        <div class="" slot="actions">
            <div class="text-3xl">
                <!-- svelte-ignore a11y_consider_explicit_label -->
                <button onclick={close_add_tithe}>
                    <i class="ri-close-circle-line cursor-pointer text-red-600"></i>
                </button>
            </div>
        </div>
        <div class="my-4" slot="content">
            <form class="ui form bg-blue-100 p-2" onsubmit={(e)=>{
                e.preventDefault();
                create_tithe();
            }}>
                <div class="two fields  flex gap-2 justify-between p-2 flex-col md:flex-row">
                     <div class="field">
                        <label for="name">Member Name</label> <br>
                        <input type="text" name="name" placeholder="John Doe" bind:value={name} readonly>
                    </div>

                    <div class="field">
                        <label for="amount">Prayer House</label> <br>
                        <select name="prayer-house" id="prayer-house" bind:value={prayerHouse} required>
                            <option value="">Select Prayer House</option>
                            {#each prayerHouses as h}
                                <option value="{h.name}">{h.name}</option>
                            {/each}
                        </select>
                    </div>
                 </div>
       
                <div class="two fields  flex gap-2 justify-between p-2 flex-col md:flex-row">
                    <div class="field">
                        <label for="amount">Tithe Amount</label> <br>
                        <input type="number" name="amount" placeholder="00.00" bind:value={titheAmount} required min="10">
                    </div>

                    <div class="field">
                         <label for="month">Tithe Month</label> <br>
                        <input type="month" name="month" placeholder="2026-01" bind:value={titheMonth} required>
                    </div>
                </div>

                <div class="two fields  flex gap-2 justify-between p-2 flex-col md:flex-row">
                    <div class="field">
                        <label for="amount">Serial No.</label> <br>
                        <input type="text" name="serial-no" placeholder="001/026" bind:value={serialNo} required>
                    </div>
                    <div class="field">
                        <label for="amount">Transaction Ref  <span class="italic text-sm text-red-400">(optional)</span> </label> <br>
                        <input type="text" name="transaction-ref" placeholder="UGMK90I7S" bind:value={transactionRef}>
                    </div>
                </div>

                <div class="field text-center py-5">
                    <button class={loading?"ui icon loading purple button":"ui icon  purple button"}>
                        <i class="send icon"></i> Add Tithe
                    </button>
                </div>
            </form>
        </div>
    </Segment>
</div>

</main>

<style>
    main{
        background: rgba(0, 0, 0, 0.377);
        backdrop-filter: blur(2px);
    }

    .content{
        max-height: 80vh;
        overflow: auto;
    }

    .field{
        flex:auto
    }

    input{
        width: 100%;
        border-radius: 5px;
        line-height: 2em;
    }
    select{
        width: 100%;
        border-radius: 5px;
        line-height: 2em;
    }
</style>