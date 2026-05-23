const stats=[];

const entities=['users','activities','events','articles','gallery'];

entities.forEach(entity=> {
    stats.push({
        title:entity,
        value:0,
        desc:""
    })
});

console.log(stats);