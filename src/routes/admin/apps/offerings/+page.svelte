<script>
	import AddTithe from "$lib/components/add-tithe.svelte";
	import SearchUser from "$lib/components/search-user.svelte";
	import Segment from "$lib/components/segment.svelte";
	import StatsCard from "$lib/components/stats-card.svelte";
	import StatsCol from "$lib/components/stats-col.svelte";
	import TithesList from "$lib/components/tithes-list.svelte";
	import { API_BASE_URL } from "$lib/config/base_urls";
	import { fetch_resource } from "$lib/methods/functions";
	import { add_commas, format_date } from "$lib/methods/methods";
	import { generate_pdf } from "$lib/methods/pdf-make.js";
	import dayjs from "dayjs";
	import { onMount } from "svelte";

    let showSearchModal=false;

    let showAdvancedFilters=false;

    let searchUser;

    let search_q;

    let tithes=[];

    let loading_data=true;

    let org_details;

    let tithe_stats=[];

    let stats=[];

    let filtered=[];

    let filter_prayer_houses=[];

    let max_filter_date=[];

    let min_filter_date=[];

    let active_filters=[];

    let pdf_title="Tithe Offerings";

    let serial;



      // Filters
    let filters = {
        search: '',
        house: 'all',
        month: '',
        dateFrom: '',
        dateTo: '',
    };

      // Pagination
        let currentPage = 1;
        let itemsPerPage = 10;
        let pageOptions = [5, 10, 25, 50, 100];


      // Filter tithes
        $: filteredTithes = tithes.filter(tithe => {
            // Search filter
            const searchMatch = filters.search === '' || 
            tithe.name.toLowerCase().includes(filters.search.toLowerCase()) ||
            tithe.house.toLowerCase().includes(filters.search.toLowerCase()) ||
            tithe.month.toLowerCase().includes(filters.search.toLowerCase());
            
            // house filter
            const houseMatch = filters.house === 'all' || tithe.house === filters.house;
            
            //month filter
            const monthMatch = filters.month === '' || tithe.month === filters.month;

            // Date filters
            const joinDate = new Date(tithe.created_at);
            const fromMatch = !filters.dateFrom || joinDate >= new Date(filters.dateFrom);
            const toMatch = !filters.dateTo || joinDate <= new Date(filters.dateTo + 'T23:59:59');
            
            pdf_title=pdf_titles().title;

            active_filters=pdf_titles().filters
 
            return searchMatch && houseMatch && monthMatch && fromMatch && toMatch ;
        });


      // Pagination
        $: totalPages = Math.ceil(filteredTithes.length / itemsPerPage);
        $: paginatedTithes = filteredTithes.slice(
            (currentPage - 1) * itemsPerPage,
            currentPage * itemsPerPage
        );
        $: startItem = (currentPage - 1) * itemsPerPage + 1;
        $: endItem = Math.min(currentPage * itemsPerPage, filteredTithes.length);
        

    const pdf_column_titles = [
		{ text: 'No', bold: true, fontSize: 9 },
		{ text: 'Name', bold: true, fontSize: 9 },
		{ text: 'Serial', bold: true, fontSize: 9 },
		{ text: 'House', bold: true, fontSize: 9 },
		{ text: 'Month', bold: true, fontSize: 9 },
		{ text: 'Phone', bold: true, fontSize: 9 },
		{ text: 'Created', bold: true, fontSize: 9 },
		{ text: 'Amount', bold: true, fontSize: 9 }
	];

	const pdf_column_widths = ['6%', '18%', '9%', '20%', '10%', '14%', '14%', '11%'];

	const pdf_make_rows = (data) => {
		let rows = [];
		data.forEach((p, i) => {
			rows.push([
				{
					text: i + 1,
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},

				{
					text: p.name,
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},

				{
					text: p.serial_no,
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},

				{
					text: p.house,
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},
				{
					text: dayjs(p.month).format('MMM YYYY'),
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},
				{
					text: p.user.phone,
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},
				{
					text: format_date(p.created_at, 'YYYY-MM-DD'),
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				},

				{
					text: add_commas(parseFloat(p.amount)),
					style: 'reportValue',
					fillColor: i % 2 == 0 ? '#f7f0f0' : '#f7f7f7'
				}
			]);
		});

        rows.push([
            {
                text:"##",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
                        {
                text:"Totals",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
                        {
                text:"--",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
                        {
                text:"--",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
             {
                text:"--",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
                        {
                text:"--",
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
                {
                text:dayjs().format('MMM DD YY'),
                style:'footerValue',
                fillColor:'#f7f7f7'
            },
              {
                text:add_commas(data.map((d)=>parseFloat(d.amount)).reduce((a,b)=>a+b,0)),
                style:'footerValue',
                fillColor:'#f7f7f7'
            }
        ])

		return rows;
	};

    const export_pdf = (data) => {
		//make rows
		let rows = pdf_make_rows(data);

		let doc_number = 'STK001';

		let title = "Tithe Offerings "+pdf_title;

		let filters = active_filters;

        generate_pdf(title,doc_number,filters,pdf_column_titles,pdf_column_widths,rows,org_details);

	};

    const pdf_titles=()=>{

        let active_filters=[];

        if(filters.search){
            active_filters.push({name:"search",value:filters.search});
        }

        if(filters.house!="all"){
            active_filters.push({name:"house",value:`from ${filters.house}`})
        }

        if(filters.month){
            active_filters.push({name:"month",value:`For ${filters.month}`})
        }

        if(filters.dateFrom){
            active_filters.push({name:"date-from",value:`From ${filters.dateFrom}`})
        }

        if(filters.dateTo){
            active_filters.push({name:"date-to",value:`To ${filters.dateTo}`})
        }

        return {
            title:active_filters.map((v)=>v.value),
            filters:active_filters
        };
    }
    const get_tithes=async()=>{
        const res=await fetch_resource('Tithes',`${API_BASE_URL}tithes.php`);

        return res.data;

    }

    const get_org=async()=>{
        const res=await fetch_resource('Org',`${API_BASE_URL}org.php`);

        return res.data;
    }



    const get_tithe_stats=async()=>{
        const res=await fetch_resource('Tithes',`${API_BASE_URL}tithes.php?stats=true`);

        return res.data;
    }

    const toggle_advanced_filters=()=>{
        showAdvancedFilters=!showAdvancedFilters
    }

    const make_filters_data=(tithes)=>{

        const filteredHouses=tithes.map((h)=>h.house);

        filter_prayer_houses=filteredHouses;
    }

    const update_serial=(tithes)=>{

        if(tithes.length<9){
             return `00${tithes.length+1}/${new Date().getFullYear().toString().slice(1)}`;

        }else if(tithes.length<100){
            return `0${tithes.length+1}/${new Date().getFullYear().toString().slice(1)}`;

        }else{
             return `${tithes.length+1}/${new Date().getFullYear().toString().slice(1)}`;
        }
    }

    onMount(async()=>{

        setTimeout(async ()=>{

        loading_data=false

        tithes=await get_tithes();

        org_details=await get_org();

        org_details.watermark="Consolata Nkubu Parish";

        tithe_stats=await get_tithe_stats();

        serial=update_serial(tithes);

        make_filters_data(tithes);

        // filteredTithes=tithes;
        
        console.log(serial);

        // console.log(tithe_stats);
        },100);

            

        

    })

</script>
<main class="my-2 md:pl-2">
    <Segment>
    <div class="" slot="title">
        Latest Tithings
    </div>

    <div class="" slot="actions">
        <div class="">
            <button onclick={()=>{
                showSearchModal=true
            }} class="ui purple icon mini button">
                <i class="plus icon"></i> New
            </button>
        </div>
    </div>
    <div class="" slot="content">
        {#if loading_data}
            <div class="">
                Loading ...
            </div>

            {:else}
            <div class="">

                <div class="my-3 py-3">
                    <StatsCol>
                    {#each tithe_stats as stat}
				        <StatsCard {stat} />
			        {/each}
                    </StatsCol>
                </div>

                <br>
                <!-- search & filter -->
                <div class="my-3 shadow-lg p-3">
                    <div class="flex gap-4 flex-col md:flex-row">
                        <div class="flex-1">
                            <input class="rounded w-full" type="search" name="search-tithes" id="search-tithes" bind:value={filters.search} placeholder="Search Tithes"/>
                        </div>

                        <div class="text-right">

                            <button onclick={()=>{
                                toggle_advanced_filters();
                            }} class={showAdvancedFilters?"ui icon blue button":"ui icon button"}>
                                <i class="filter icon"></i> {showAdvancedFilters?"Hide":"Show"} Filters
                            </button>

                            <!-- svelte-ignore a11y_consider_explicit_label -->
                            <button  onclick={()=>{
                                showSearchModal=true
                            }} class="ui basic purple button icon mini p-2">
                                <i class="plus icon"></i>
                            </button>

                            <!-- svelte-ignore a11y_consider_explicit_label -->
                            <button class="ui basic green button icon mini p-2">
                                <i class="excel file icon"></i>
                            </button>

                            <!-- svelte-ignore a11y_consider_explicit_label -->
                            <button onclick={()=>{
                                export_pdf(filteredTithes);
                            }} class="ui basic red button icon mini p-2">
                                <i class="pdf file icon"></i>
                            </button>
                        </div>
                    </div>

                    {#if showAdvancedFilters}
                        <div class="my-5 p-2">
                            <div class="flex justify-between">
                                <div class="text-lg font-extrabold">
                                <i class="filter icon"></i> Advanced Filters
                                </div>

                                <div class="">
                                    <button onclick={()=>{
                                        filtered=tithes;
                                    }} class="ui mini icon button basic red">
                                        <i class="delete icon"></i> Clear All
                                    </button>
                                </div>
                            </div>

                            <br>
                            <div class="">
                                <form class="ui form">
                                    <div class="fields flex gap-5">
                                        <div class="field">
                                            <label for="prayer-house" class="font-bold my-3">Prayer House</label> <br>
                                            <select name="filter-house" id="filter-house" bind:value={filters.house}>
                                                <option value="all">All</option>

                                                {#each filter_prayer_houses as house}
                                                    <option value="{house}">{house}</option>
                                                {/each}
                                            </select>
                                        </div>

                                        <div class="field">
                                            <label for="tithe-month" class="font-bold my-3">Tithe Month</label> <br>
                                            <input type="month" name="filter-tithe-month" id="filter-tithe-month" bind:value={filters.month}>
                                        </div>

                                        <div class="field">
                                            <label for="date-added" class="font-bold my-3">Create Date From</label> <br>
                                            <input type="date" name="filter-tithe-date-from" id="filter-tithe-date-from" bind:value={filters.dateFrom}>
                                        </div>

                                        <div class="field">
                                            <label for="date-added" class="font-bold my-3">Create Date To</label> <br>
                                            <input type="date" name="filter-tithe-date-to" id="filter-tithe-date-to " bind:value={filters.dateTo}>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>                        
                    {/if}


                </div>
                <!-- search & filter -->
                <br>
                <div class="my-3 shadow-lg p-3">
                    <TithesList tithes={paginatedTithes}/>
                </div>
                <div class="">
                          <!-- Pagination -->
                <div class="pagination-container">
                    <div class="pagination-info">
                    Showing {startItem} to {endItem} of {filteredTithes.length} entries
                    {#if filters.search || filters.role !== 'all' || filters.status !== 'all'}
                        <span class="filtered-info">
                        (filtered from {tithes.length} total tithes)
                        </span>
                    {/if}
                    </div>
                    
                    <div class="pagination-controls">
                    <div class="pagination-size">
                        <label for="page-size">Show:</label>
                        <select bind:value={itemsPerPage} class="page-size-select">
                        {#each pageOptions as option}
                            <option value={option}>{option}</option>
                        {/each}
                        </select>
                    </div>
                    
                    <div class="pagination-buttons">
                        <!-- svelte-ignore a11y_consider_explicit_label -->
                        <button 
                        onclick={() => currentPage = 1}
                        disabled={currentPage === 1}
                        class="pagination-btn"
                        >
                        <i class="fas fa-angle-double-left"></i>
                        </button>
                        
                        <!-- svelte-ignore a11y_consider_explicit_label -->
                        <button 
                        onclick={() => currentPage--}
                        disabled={currentPage === 1}
                        class="pagination-btn"
                        >
                        <i class="fas fa-angle-left"></i>
                        </button>
                        
                        {#each Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                        let pageNum;
                        if (totalPages <= 5) {
                            pageNum = i + 1;
                        } else if (currentPage <= 3) {
                            pageNum = i + 1;
                        } else if (currentPage >= totalPages - 2) {
                            pageNum = totalPages - 4 + i;
                        } else {
                            pageNum = currentPage - 2 + i;
                        }
                        return pageNum;
                        }) as pageNum}
                        <button 
                            onclick={() => currentPage = pageNum}
                            class="pagination-btn {currentPage === pageNum ? 'active' : ''}"
                        >
                            {pageNum}
                        </button>
                        {/each}
                        
                        <!-- svelte-ignore a11y_consider_explicit_label -->
                        <button 
                        onclick={() => currentPage++}
                        disabled={currentPage === totalPages}
                        class="pagination-btn"
                        >
                        <i class="fas fa-angle-right"></i>
                        </button>
                        
                        <!-- svelte-ignore a11y_consider_explicit_label -->
                        <button 
                        onclick={() => currentPage = totalPages}
                        disabled={currentPage === totalPages}
                        class="pagination-btn"
                        >
                        <i class="fas fa-angle-double-right"></i>
                        </button>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        {/if}
    </div>
    </Segment>
</main>

{#if showSearchModal}
    <SearchUser 
        on:close={()=>{
            searchUser=undefined;
            showSearchModal=false;
        }}
        on:search={(e)=>{
            searchUser=e.detail;

            console.log(searchUser);

            showSearchModal=false;
    }}/>
{/if}


{#if searchUser}
    <AddTithe user={searchUser} {serial} on:close={async()=>{
        tithes=await get_tithes();
        serial=update_serial(tithes);
        searchUser=undefined;
        showSearchModal=false;
    }}/>
{/if}

<style>
    .field{
        flex: 1;
    }

    input{
        width: 100%;
    }
    select{
        width: 100%;
    }

     /* Pagination */
  .pagination-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-top: 1px solid #e2e8f0;
    flex-wrap: wrap;
    gap: 16px;
  }

  .pagination-info {
    color: #64748b;
    font-size: 0.9rem;
  }

  .filtered-info {
    color: #94a3b8;
    font-size: 0.85rem;
  }

  .pagination-controls {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .pagination-size {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .page-size-select {
    padding: 6px 10px;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    background: white;
  }

  .pagination-buttons {
    display: flex;
    gap: 4px;
  }

  .pagination-btn {
    min-width: 36px;
    height: 36px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    color: #475569;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  .pagination-btn:hover:not(:disabled) {
    background: #f1f5f9;
    border-color: #cbd5e1;
  }

  .pagination-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
  }

  .pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

</style>