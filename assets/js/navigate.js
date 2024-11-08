const id = ['teams', 'circuits', 'drivers', 'statistics'];
for (let i=0; i<id.length; ++i){
    next_page(id[i]);
}

function next_page(id){
    $(`#${id}`).on("click", () => {
        switch (id){
            case('teams') : window.location.href = "/f1-webapp/views/public/teams.php"; break;
            case('circuits') : window.location.href = "/f1-webapp/views/public/circuits.php"; break
            case('drivers') : window.location.href = "/f1-webapp/views/public/drivers.php"; break;
            case('statistics') : window.location.href = "/f1-webapp/views/public/statistics.php"; break;
        }
    })
}