function f1_scrape_info_drivers(id, url) {
    $(`#button${id}`).on("click", () => {
        fetch(`http://localhost:63343/f1-webapp/controllers/drivers/info_drivers.php?url=${encodeURIComponent(url)}`)
            .then(response => response.json())
            .then(data => {
                // console.log(data);
                if (data) {
                    const size = {width: 0, height: 0};
                    const div = document.getElementById('div' + id);

                    size.width = div.offsetWidth;
                    size.height = div.offsetHeight;
                    const ul_size = size.height - 128;

                    const button_position = document.getElementById('button' + id).offsetTop;
                    const hr = document.getElementById('hr' + id);
                    const hr_position_top = hr.offsetTop - 15;
                    const hr_position_left = hr.offsetLeft;
                    const hr_width = hr.offsetWidth;

                    const info = ["Team", "Country", "Podiums", "Points", "Grands Prix entered", "World Championships",
                        "Highest race finish", "Highest grid position", "Date of birth", "Place of birth"];
                    let tmp = "";
                    for (let i = 0; i < data.length; i++) {
                        tmp += `<li style="padding-bottom: 7%;"><strong>${info[i]}: </strong>${data[i]}</li>`
                    }
                    const info_html = `<div id="main_div" style="max-height: ${size.height}px; max-width: ${size.width}px;" class="card-body d-flex justify-content-center align-items-center">
                                <div style="max-height: ${size.height}px; max-width: ${size.width}px;">
                                    <ul style="max-height: ${ul_size}px;">` +
                                tmp
                                + `</ul>
                                <hr id="hr${id}" style="position: absolute; top: ${hr_position_top}px; left: ${hr_position_left}px; width: ${hr_width}px">
                                <div class="d-flex justify-content-center">
                                    <button id="button_id${id}" style="position: absolute; top: ${button_position}px;" type="button" class="btn card-link text-decoration-none d-flex justify-content-center">
                                            <span class="my_outline_animation d-flex flex-row gap-2 pb-1 hover-red">
                                                <span class="material-symbols-outlined">keyboard_double_arrow_right</span>
                                                Show driver
                                                <span class="material-symbols-outlined">sports_score</span>
                                            </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            show_driver(${id});
                            document.getElementById('div${id}').style.position = 'relative';
                            document.getElementById('div${id}').style.height = '${size.height}px';
                        </script>`

                    //console.log(info_html);
                    $(`#num${id}`).addClass('d-none');
                    $(`#info_driver${id}`).removeClass('d-none').html(info_html);
                }
            })
            .catch(error => {
                console.error('Error in API call:', error);
            });
    });
}

window.addEventListener('orientationchange', () => {
    window.location.reload()
})

function show_driver(id) {
    $(`#button_id${id}`).on("click", () => {
        $(`#num${id}`).removeClass('d-none');
        $(`#info_driver${id}`).addClass('d-none');
    });
}