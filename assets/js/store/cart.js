/** CART page RENDER */
// $(document).ready()
$().ready(() => {
    render_cart();
    $(".lds-ring-container").addClass("d-none");
    $("#cart-list").removeClass("d-none");
    // Storage event listener, so the cart can be kept updated as soon as the user adds a new product
    window.addEventListener('storage', function() {
        const cart = JSON.parse(localStorage.getItem(`cart-${user_id}`));
        render_cart(cart);
    })
})

/** Loading effect after confirming order */
$("#form-loading").on('submit', event => {
    // :eq(1) used to select the second loading circle
    $(".lds-ring-container").eq(1).removeClass("d-none");
})

/** Remove item from cart (LocalStorage) */
const remove_on_click = (cart = curr_cart) => {
    $(".remove").click(event => {
        event.preventDefault();
        // Find ID to remove through the index of the "remove button" (e.g. "remove-7" refers to the item with ID 7)
        const id_to_remove = event.target.id.split("-")[1];
        const size_to_remove = event.target.id.split("-")[2];
        // Find product by ID the product to remove from current cart
        // splice(start, deleteCount)
        const index = cart.findIndex(item => {
            return item.id === id_to_remove && item.size === size_to_remove;
        });

        cart[index].quantity--;
        if (cart[index].quantity < 1) {
            cart.splice(index, 1)
        }

        // Set new current cart in LocalStorage and render changes
        localStorage.setItem(`cart-${user_id}`, JSON.stringify(cart));
        const items_cnt = items_cart(cart);
        $("#cart-notification-dot").text((items_cnt > 0)? items_cnt:"");
        render_cart(cart);
    })
}

const update_info = (info, item) => {
    info.ids += item.id + "\t";
    info.titles += item.title + "\t";
    info.teams += item.team_name + "\t";
    info.quantities += item.quantity + "\t";
    info.sizes += item.size + "\t";
    info.imgs += item.img_url.split('\t')[0] + "\t";
    info.prices += item.price + "\t";
    info.total_price += (item.price * item.quantity);
}

const update_input_fields = info => {
    $("#ids").val(info.ids);
    $("#titles").val(info.titles);
    $("#teams").val(info.teams);
    $("#quantities").val(info.quantities);
    $("#sizes").val(info.sizes);
    $("#imgs").val(info.imgs);
    $("#prices").val(info.prices);
    $("#total").val(info.total_price);
    $(".total-price").text(str2int_dec(info.total_price) + " €");
}

const render_cart = (cart = curr_cart) => {
    // Delete all child nodes
    $("#cart-list").empty();
    let info = {
        "ids": "",
        "titles": "",
        "teams": "",
        "quantities": "",
        "sizes": "",
        "imgs": "",
        "prices": "",
        "total_price": 0
    }

    if (cart && cart.length) {
        $("#second-body").removeClass("d-none")
        $("#cart-empty-alert").addClass("d-none");
        cart.forEach(item => {
            update_info(info, item);
            $("#cart-list").prepend(
                $(`<div id='element-${item.id}' class='row d-flex justify-content-center gap-4 item'>` +
                        html_img(item.id, item.img_url) +
                        html_description(item.id, item.title, item.team_name, item.price, item.size, item.quantity) +
                        html_price(item.price) +
                        "<hr class='d-md-none rounded my-thick-grey'>" +
                    "</div>")
            )
        })
        // Add event listener on "click" to each .remove button
        remove_on_click(cart);
        update_input_fields(info);
        const curr_cart_length = items_cart(cart);
        $("#count-items").text(curr_cart_length + ` product${curr_cart_length > 1? "s":""}`)

    } else {
        $("#cart-empty-alert").removeClass("d-none");
        $("#second-body").addClass("d-none")
    }
}

const html_img = (id, img_url) => {
    return `<div class="col-12 col-md-4 col-lg-3 text-center text-md-end">
                <a href="/f1-webapp/views/public/store/product.php?id=${id}" target="_blank" class="text-decoration-none">
                    <img height="200px" class="product-img" src="${img_url}" alt="">
                </a>
            </div>`
}

const html_description = (id, title, team, price, size, quantity) => {
    return `<div class="col-12 col-md-4 text-center text-md-start">
                <a href="/f1-webapp/views/public/store/product.php?id=${id}" target="_blank" class="link-product w-100 text-decoration-none text-light d-flex flex-column justify-content-between align-items-start">
                    <span class="mx-auto mx-md-0 w-100">
                        ${title}
                        <hr class="rounded my-thin-grey">
                        ${team}
                    </span>
                    <br>
                    <div class="w-100 d-flex justify-content-around justify-content-md-between align-items-center">
                        <div class="mt-3">
                            Quantity: <strong>${quantity}</strong>
                            <br>
                            Size: ${size.toUpperCase()}
                        </div>
                        <button id="remove-${id}-${size.toLowerCase()}" class="remove bg-transparent border-0 p-0 text-info d-flex gap-2 mt-3">
                            <span class="material-symbols-outlined">delete</span>
                            Remove
                        </button>
                        <strong class="mt-3 d-md-none">${str2int_dec(price)} €</strong>        
                    </div>
                </a>
            </div>`
}

const html_price = price => {
    return `<div class="d-none d-md-block col-12 col-md-2 text-start ms-auto">
                    <strong>${str2int_dec(price)} €</strong>
                </div>`
}

const str2int_dec = str => {
    const int = Math.floor(str / 100);
    // Decimal part obtained with module operator
    // If it is less than 10, I need to add "0" character in order to obtain "05", and not "5" to indicate 5 cents ("5" should represent 50 cents)
    // otherwise, the value returned by the module operator is already good
    // e.g. price = 7505
    //          int = "75"
    //          dec = "05"
    // output wanted = "75.05"
    const dec = (str%100 < 10)? ("0" + str % 100) : str % 100
    return `${int}.${dec}`;
}