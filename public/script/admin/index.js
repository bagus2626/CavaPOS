$(document).ready(function () {
    displayData();
});

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

const getUrl = () => {
    let url = window.location.href;
    let arr = url.split("/");
    let data = arr[4];
    console.log(data)

    return data;
};

const data = getUrl();

const numberWithCommas = x => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

function displayData(id = null, data_search = null, type = "biasa") {
    $.ajax({
        url: `/admin/${data}/create`,
        method: "get",
        success: async function (response) {
            await $(`#show-data-${data}`).html(response);
            $(`#row-${data}-${id}`).addClass("success-alert");
            setTimeout(() => {
                $(`#row-${data}-${id}`).removeClass("success-alert");
            }, 3000);
            if (data == "pick-point") {
                $(`#table-pick-point-suites`).DataTable();
                $(`#table-pick-point-executive`).DataTable();
                $(`#table-pick-point-big-top`).DataTable();
            } else {
                $(`#table-${data}`).DataTable();
            }
        },
        error: function (err) {
            console.log(err);
        },
    });
}

let modalType = null;

function openModal(data, type, id = null) {
    if (type == "add") {
        $(`#modal-${data}`).modal("show");
    } else if (type == "edit") {
        $(`#modal-${data}`).LoadingOverlay("show");
        $(`#modal-${data}`).modal("show");
        modalType = type;
    } else if (type == "back") {
        $(`#form-data-${data}`).hide();
        $(`#show-data-${data}`).show();
        $(`#btn-add-${data}`).show();
        $(`#btn-back-${data}`).hide();
    } else if (type == "detail") {
        $(`#modal-detail-${data}`).LoadingOverlay("show");
        $(`#modal-detail-${data}`).modal("show");
    } else if (type == "gallery") {
        $(`#modal-gallery-${data}`).modal("show");
    }

    switch (type) {
        case "add":
            $(`#form-${data}`)[0].reset();
            $(`#add-${data}`).show();
            $(`#edit-${data}`).hide();
            if (data === "tripcode") {
                handleDestination($("#destination").val());
                $("#row-start-date-active").show();
                $("#row-destination").show();
                $("#row-start-date").hide();
                $("#row-finish-date").hide();
                $("#row-price").hide();
                $("#row-qty").hide();
                $("#row-event-name").hide();
            } else if (data == "promotion") {
                $(`.promo_nominal`).mask("#.###.##0", {reverse: true});
            }
            $.ajax({
                url: `/admin/master-data/${data}/code`,
                method: "get",
                dataType: "json",
                success: async function (response) {
                    let code = `${data}`;

                    if (data.includes("-")) {
                        code = data.replaceAll("-", "_");
                    }

                    if (data == "promotion") {
                        await $(`#promo_code`).val(response.data);
                    }

                    await $(`#${code}_code`).val(response.data);

                    if (data == "blog") {
                        $(`#form-data-${data}`).LoadingOverlay("hide");
                    } else {
                        $(`#modal-${data}`).LoadingOverlay("hide");
                    }
                },
                error: function (err) {
                    console.log(err);
                },
            });
            break;
        case "edit":
            $(`#form-${data}`)[0].reset();
            $(`#edit-${data}`).show();
            $(`#add-${data}`).hide();
            if (data == "promotion") {
                $(`.promo_nominal`).mask("#.###.##0", {reverse: true});
            }
            $.ajax({
                url: `/admin/master-data/${data}/${id}/edit`,
                method: "get",
                dataType: "json",
                success: async function (response) {
                    if (data == "blog") {
                        $(`#form-data-${data}`).LoadingOverlay("hide");
                    } else {
                        $(`#modal-${data}`).LoadingOverlay("hide");
                    }
                    await fetchData(data, response.data);
                },
                error: function (err) {
                    console.log(err);
                },
            });
            break;
        case "detail":
            $(`#modal-detail-${data}`).LoadingOverlay("hide");
            $("#detail-specification").hide();
            $.ajax({
                url: `/admin/master-data/${data}/${id}/edit`,
                method: "get",
                dataType: "json",
                success: async function (response) {
                    await fetchData(data, response.data, "DETAIL");
                },
                error: function (err) {
                    console.log(err);
                },
            });
            break;
        case "gallery":
            $.ajax({
                url: `/admin/master-data/${data}/${id}/edit`,
                method: "get",
                dataType: "json",
                success: async function (response) {
                    await fetchData(data, response.data, "GALLERY");
                },
                error: function (err) {
                    console.log(err);
                },
            });
            break;
    }
}

const successResponse = (type, data, message, id = null) => {
    $(`#modal-${data}`).modal("hide");
    $(`#form-${data}`)[0].reset();
    $(`#form-${data}`).unbind("submit");
    displayData(id);

    if (data == "blog") {
        CKEDITOR.instances["blog_content"].setData("");
        $(`#form-data-${data}`).hide();
        $(`#show-data-${data}`).show();
        $(`#btn-add-${data}`).show();
        $(`#btn-back-${data}`).hide();
    }
    Toast.fire({
        icon: "success",
        title: message,
    });
    switch (type) {
        case "add":
            $(`#add-${data}`).removeAttr("disabled");
            break;
        default:
            break;
    }
};

function manageData(type, id = null) {
    if (data == "promotion") {
        $(`.promo_nominal`).unmask();
    }
    switch (type) {
        case "save":
            $(`#form-${data}`)
                .off("submit")
                .on("submit", function (e) {
                    e.preventDefault(); // ✅ Pastikan hanya dipanggil sekali
                    $(`#add-${data}`).attr("disabled", true);

                    let formData = new FormData(this);
                    $.ajax({
                        url: `/admin/master-data/${data}`,
                        type: "post",
                        data: formData,
                        dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: async function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Sukses",
                                text: response.message,
                            });

                            await successResponse(
                                "add",
                                data,
                                response.message,
                                response.data.id
                            );
                            $(`#add-${data}`).attr("disabled", false);
                        },
                        error: async function (err) {
                            console.log(err); // ✅ Debug error di console

                            let errorMessage = "Terjadi kesalahan pada server.";
                            if (err.status === 422 && err.responseJSON.errors) {
                                errorMessage = "";
                                let err_log = err.responseJSON.errors;
                                for (let field in err_log) {
                                    errorMessage += `- ${err_log[field][0]}\n`;
                                }
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Simpan Data Gagal!",
                                text: errorMessage,
                            });

                            $(`#add-${data}`).attr("disabled", false);
                        },
                    });
                });
            break;
        case "update":
            id = $("#id").val();
            if (
                data === "principal" ||
                data === "product" ||
                data === "product-boditech" ||
                data === "featured-product"
            ) {
                $(`#form-${data}`).submit(function (e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    let url = `/admin/master-data/${data}/update/edit-${data}`;
                    $.ajax({
                        url: url,
                        type: "post",
                        data: formData,
                        dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: async function (response) {
                            await successResponse(
                                "edit",
                                data,
                                response.message,
                                response.data.id
                            );
                        },
                        error: async function (err) {
                            console.log(err);
                            let err_log = err.responseJSON.errors;
                            await handleError(err, err_log, data);
                        },
                    });
                });
            } else if (data == "bus") {
                $(`#form-${data}`).submit(function (e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    formData.append('_method', 'PATCH');
                    $.ajax({
                        url: `/admin/master-data/${data}/${id}`,
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        contentType: false,
                        processData: false,
                        success: async function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Sukses",
                                text: response.message,
                            });

                            await successResponse(
                                "add",
                                data,
                                response.message,
                                response.data.id
                            );
                            $(`#add-${data}`).attr("disabled", false);
                        },
                        error: async function (err) {
                            console.log(err); // ✅ Debug error di console

                            let errorMessage = "Terjadi kesalahan pada server.";
                            if (err.status === 422 && err.responseJSON.errors) {
                                errorMessage = "";
                                let err_log = err.responseJSON.errors;
                                for (let field in err_log) {
                                    errorMessage += `- ${err_log[field][0]}\n`;
                                }
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Simpan Data Gagal!",
                                text: errorMessage,
                            });

                            $(`#add-${data}`).attr("disabled", false);
                        },
                    });
                });
            } else {
                $(`#form-${data}`).submit(function (e) {
                    e.preventDefault();
                    let formData = $(`#form-${data}`).serialize();
                    $.ajax({
                        url: `/admin/master-data/${data}/${id}`,
                        type: "patch",
                        data: formData,
                        dataType: "json",
                        success: async function (response) {
                            Swal.fire({
                                icon: "success",
                                title: "Sukses",
                                text: response.message,
                            });

                            await successResponse(
                                "add",
                                data,
                                response.message,
                                response.data.id
                            );
                            $(`#add-${data}`).attr("disabled", false);
                        },
                        error: async function (err) {
                            console.log(err); // ✅ Debug error di console

                            let errorMessage = "Terjadi kesalahan pada server.";
                            if (err.status === 422 && err.responseJSON.errors) {
                                errorMessage = "";
                                let err_log = err.responseJSON.errors;
                                for (let field in err_log) {
                                    errorMessage += `- ${err_log[field][0]}\n`;
                                }
                            }

                            Swal.fire({
                                icon: "error",
                                title: "Simpan Data Gagal!",
                                text: errorMessage,
                            });

                            $(`#add-${data}`).attr("disabled", false);
                        },
                    });
                });
            }

            break;
        case "delete":
            Swal.fire({
                title: "Yakin akan menghapus data?",
                text: "Data yang di hapus tidak dapat dikembalikan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/master-data/${data}/${id}`,
                        method: "delete",
                        success: function (response) {
                            if (response.status == 300) {
                                Toast.fire({
                                    icon: "error",
                                    title: response.message,
                                });
                                return;
                            }

                            successResponse(
                                "delete",
                                data,
                                response.message,
                                response.data
                            );
                        },
                        error: function (err) {
                            console.log(err);
                        },
                    });
                }
            });
            break;
        case "activate":
            id = $("#id").val();
            Swal.fire({
                title: "Yakin akan mengaktifkan data?",
                text: "Data yang di aktif tidak dapat dikembalikan",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, saya yakin!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/master-data/${data}/${id}`,
                        method: "get",
                        dataType: "json",
                        success: async function (response) {
                            await successResponse(
                                "activate",
                                data,
                                response.message,
                                response.data.id
                            );
                        },
                        error: function (err) {
                            console.log(err);
                        },
                    });
                }
            });
            break;
        default:
            break;
    }
}

function handleError(err, err_log, type) {
    $(`#add-${data}`).removeAttr("disabled");
    $(`#form-${data}`).unbind("submit");
    switch (type) {
        case "status":
            if (err.status == 422) {
                if (typeof err_log.status_name !== "undefined") {
                    Toast.fire({
                        icon: "error",
                        title: err_log.status_name[0],
                    });
                }
                if (typeof err_log.status_code !== "undefined") {
                    Toast.fire({
                        icon: "error",
                        title: err_log.status_code[0],
                    });
                }
            } else {
                Toast.fire({
                    icon: "error",
                    title: "Terjadi Kesalahan Pada Sistem",
                });
            }
            break;
    }
}

function fetchData(data, response, type = null) {
    switch (data) {
        case "category-blog":
            $("#id").val(response.id);
            $("#category_blog_code").val(response.category_blog_code);
            $("#category_blog_name").val(response.category_blog_name);
            $("#category_blog_description").val(
                response.category_blog_description
            );
            break;
        case "status":
            $("#id").val(response.id);
            $("#status_code").val(response.status_code);
            $("#status_name").val(response.status_name);
            $("#status_description").val(response.status_description);
            break;
        case "bbm":
            $("#id").val(response.id);
            $("#status").val(response.status);
            $("#deskripsi").val(response.deskripsi);
            break;
        case "agen-pool":
            $("#id").val(response.id);
            $("#name").val(response.name);
            $("#lokasi").val(response.lokasi);
            $("#number_phone").val(response.number_phone);
            $("#deskripsi").val(response.deskripsi);
            $("#area").val(response.area);
            $("#link_maps").val(response.link_maps);
            break;
        case "department":
            $("#id").val(response.id);
            $("#department_code").val(response.department_code);
            $("#department_name").val(response.department_name);
            $("#department_description").val(response.department_description);
            break;
        case "position":
            $("#id").val(response.id);
            $("#position_code").val(response.position_code);
            $("#position_name").val(response.position_name);
            $("#id_department").val(response.id_department);
            $("#position_description").val(response.position_description);
            break;
        case "facility":
            $("#id").val(response.id);
            $("#facility_code").val(response.facility_code);
            $("#facility_name").val(response.facility_name);
            $("#facility_description").val(response.facility_description);
            break;
        case "pasengger_luggage":
            $("#id").val(response.id);
            $("#pasengger_luggage_code").val(response.pasengger_luggage_code);
            $("#pasengger_luggage_name").val(response.pasengger_luggage_name);
            $("#pasengger_luggage_price").val(response.pasengger_luggage_price);
            $("#pasengger_luggage_description").val(
                response.pasengger_luggage_description
            );
            break;
        case "unit":
            $("#id").val(response.id);
            $("#unit_code").val(response.unit_code);
            $("#unit_name").val(response.unit_name);
            $("#unit_alias").val(response.unit_alias);
            $("#unit_description").val(response.unit_description);
            break;
        case "service":
            $("#id").val(response.id);
            $("#service_code").val(response.service_code);
            $("#service_name").val(response.service_name);
            $("#service_description").val(response.service_description);
            break;
        case "premi":
            $("#id").val(response.id);
            $("#premi_code").val(response.premi_code);
            $("#premi_name").val(response.premi_name);
            $("#premi_amount").val(response.premi_amount);
            $("#premi_bonus_pnp_15").val(response.premi_bonus_pnp_15);
            $("#premi_bonus_pnp_full").val(response.premi_bonus_pnp_full);
            $("#premi_type").val(response.premi_type);
            $("#premi_employee").val(response.premi_employee);
            break;
        case "salary":
            $("#id").val(response.id);
            $("#salary_code").val(response.salary_code);
            $("#salary_name").val(response.salary_name);
            $("#salary_amount").val(response.salary_amount);
            $("#salary_description").val(response.salary_description);
            break;
        case "armada":
            $("#id").val(response.id);
            $("#armada_category").val(response.armada_category);
            $("#armada_year").val(response.armada_year);
            $("#armada_capacity").val(response.armada_capacity);
            $("#armada_cylinder").val(response.armada_cylinder);
            $("#armada_type").val(response.armada_type);
            $("#armada_seat").val(response.armada_seat);
            $("#armada_merk").val(response.armada_merk);
            $("#armada_no_police").val(response.armada_no_police);

            //supaya armada type tidak harus dipilih ulang saat update modal.
            handleArmadaType(
                document.getElementById("armada_category"),
                data.armada_type
            );
            $("#modal-armada").modal("show");
            $("#armada_type").val(response.armada_type);
            break;
        case "bus":
            $("#id").val(response.id);
            $("#bus_code").val(response.bus_code);
            $("#bus_type").val(response.bus_type);
            $("#bus_capacity").val(response.bus_capacity);
            $("#bus_name").val(response.bus_name);
            $("#bus_price").val(response.bus_price);
            $("#bus_description").val(response.bus_description);

            $("#data-image-bus").html("");
            response.gallery.map((el, i) => {
                let bus_photo_src = `../../storage/${el.bus_photo}`;
                $("#data-image-bus").append(
                    `<img class="w-100" style="margin-bottom: 5px" src='` +
                    bus_photo_src +
                    `'/>`
                );
            });

            let src = `../../storage/${response.bus_image}`;
            $("#bus_gallery_bus_image").attr("src", src);

            $("#bus_gallery_bus_code").val(response.bus_code);
            $("#bus_gallery_bus_type").val(response.bus_type);
            $("#bus_gallery_bus_capacity").val(response.bus_capacity);
            $("#bus_gallery_bus_name").val(response.bus_name);
            $("#bus_gallery_description").val(response.bus_description);

            let facility_bus = response.bus_facility;
            let ex_facility = facility_bus.split(", ");
            let html_facility = ``;
            let selectedFacility = new Array();

            ex_facility.forEach((facility, i) => {
                selectedFacility[i] = $(
                    `#bus_facilities option:contains('${facility}')`
                ).val();
                html_facility += `<div class="badge badge-primary mr-1 mb-1">${facility}</div>`;
            });
            $("#bus_gallery_facility").html(html_facility);
            $("#bus_facilities")
                .select2()
                .val(selectedFacility)
                .trigger("change");

            // Gallery
            let gallery = response.gallery;
            let htmlGallery = `<ol class="carousel-indicators">`;
            let indexGall = 0;
            let indexGallPhoto = 0;
            gallery.forEach((gall) => {
                htmlGallery += `<li data-target="#carousel-example-generic" data-slide-to="${indexGall}" class="${
                    indexGall == 0 ? "active" : ""
                }"></li>`;
                indexGall++;
            });
            htmlGallery += `</ol><div class="carousel-inner" role="listbox">`;
            gallery.forEach((gallPhoto) => {
                let srcGallery = `../../storage/${gallPhoto.bus_photo}`;
                htmlGallery += ` <div class="carousel-item ${
                    indexGallPhoto == 0 ? "active" : ""
                }"> <img class="img-fluid" src="${srcGallery}" alt="${
                    gallPhoto.bus_photo
                }"> </div>`;
                indexGallPhoto++;
            });
            htmlGallery += `</div>`;
            $("#carousel-example-generic").html(htmlGallery);
            break;
        case "office":
            $("#id").val(response.id);
            $("#office_code").val(response.office_code);
            $("#office_origin").val(response.office_origin);
            $("#office_name").val(response.office_name);
            $("#office_phone").val(response.office_phone);
            $("#office_address").val(response.office_address);
            break;
        case "pick-point":
            $("#id").val(response.id);
            $("#pick_point_code").val(response.pick_point_code);
            $("#pick_point_origin").val(response.pick_point_origin);
            $("#pick_point_name").val(response.pick_point_name);
            $("#pick_point_eta").val(response.pick_point_eta);
            $("#pick_point_zone").val(response.pick_point_zone);
            $("#pick_point_description").val(response.pick_point_description);
            $("#pick_point_type_bus").val(response.pick_point_type_bus);
            $("#pick_point_status").val(response.pick_point_status);
            break;
        case "tripcode":
            $("#id").val(response.id);
            $("#tripcode_code").val(response.tripcode);
            if (response.is_events === "Y") {
                $("#isEvent").prop("checked", true);
                $("#noEvent").prop("checked", false);
                $("#row-destination").show();
                $("#row-start-date").show();
                $("#row-finish-date").show();
                $("#row-price").show();
                $("#row-qty").hide();
                $("#row-event-name").show();
                $("#row-start-date-active").hide();
            } else if (response.is_events === "N") {
                $("#isEvent").prop("checked", false);
                $("#noEvent").prop("checked", true);
                $("#row-start-date-active").show();
                $("#row-destination").show();
                $("#row-start-date").hide();
                $("#row-finish-date").hide();
                $("#row-price").hide();
                $("#row-qty").hide();
                $("#row-event-name").hide();
            }

            $("#name").val(response.name);
            $("#price").val(response.price ?? 0);
            $("#start_date").val(response.start_date);
            $("#finish_date").val(response.finish_date);
            $("#start_date_active").val(response.start_date_active);
            $("#bus_id").val(response.bus_id);
            $("#destination").val(response.destination);

            $("#row-pick-point-select").show();
            $("#row-arrival-point-select").show();
            /*$("#pick_point_id").val(response.pick_point_id);
            $("#arrival_point_id").val(response.arrival_point_id);*/

            let destination = $("#destination").val();
            let bus_name = $(`#bus_id option:selected`).data("bus_name");

            dataPoint(
                destination,
                bus_name,
                response.pick_point_id,
                response.arrival_point_id
            );
            break;
        case "day-off":
            $("#id").val(response.id);
            $("#day_off_code").val(response.day_off_code);
            $("#day_off_name").val(response.day_off_name);
            $("#day_off_date").val(response.day_off_date);
            $("#day_off_description").val(response.day_off_description);
            break;
        case "schedule":
            $("#id").val(response.id);
            $("#schedule_code").val(response.schedule_code);
            $("#schedule_bus").val(response.schedule_bus);
            $("#schedule_destination").val(response.schedule_destination);
            $("#schedule_day").val(response.schedule_day);
            $("#schedule_description").val(response.schedule_description);
            break;
        case "destination-wisata":
            $("#id").val(response.id);
            $("#destination_wisata_province").val(response.destination_wisata_province);
            $("#destination_wisata_code").val(response.destination_wisata_code);
            $("#destination_wisata_name").val(response.destination_wisata_name);
            $("#destination_wisata_description").val(response.destination_wisata_description);
            break;
        case "route-wisata":
            $("#id").val(response.id);
            $("#route_wisata_from").val(response.route_wisata_from);
            $("#route_wisata_target").val(response.route_wisata_target);
            $("#route_wisata_price").val(response.route_wisata_price);
            $("#route_wisata_estimate").val(response.route_wisata_estimate);
            $("#route_wisata_description").val(response.route_wisata_description);
            break;
        case "travel-facility":
            $("#id").val(response.id);
            $("#travel_facility_code").val(response.travel_facility_code);
            $("#travel_facility_name").val(response.travel_facility_name);
            $("#travel_facility_description").val(response.travel_facility_description);
            break;
        case "promotion":
            $("#id").val(response.id);
            $("#promo_code").val(response.promo_code);
            $("#promo_name").val(response.promo_name);
            $("#promo_start_date").val(response.promo_start_date);
            $("#promo_end_date").val(response.promo_end_date);
            $("#promo_type").val(response.promo_type);
            $("#promo_nominal").val(numberWithCommas(response.promo_nominal));
            $("#promo_description").val(response.promo_description);
            break;
        case "route-bus-exclusion":
            $("#id").val(response.id);
            $("#route_bus_exclusion_code").val(response.exclusion_code);
            $("#route_bus_exclusion_bus_type").val(response.bus_type);
            $("#route_bus_exclusion_route").val(response.route);
            $("#route_bus_exclusion_start_date").val(response.start_date);
            $("#route_bus_exclusion_end_date").val(response.end_date);
            $("#route_bus_exclusion_description").val(response.description);
            break;
    }
}

function detailSpecification() {
    $("#detail-specification").show();
    let id = $("#id-detail").val();
    $.ajax({
        url: `/admin/master-data/${data}/${id}/edit`,
        method: "get",
        dataType: "json",
        success: async function (response) {
            console.log(response);
        },
        error: function (err) {
            console.log(err);
        },
    });
}

const formatDate = (data_date) => {
    let DATE_FORMAT = new Date(data_date);

    const dtf = new Intl.DateTimeFormat("en", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
    const [{value: month}, , {value: date}, , {value: year}] =
        dtf.formatToParts(DATE_FORMAT);

    let VALUE_DATE = `${date} ${month} ${year}`;
    return VALUE_DATE;
};

function convertSlug() {
    let value = $("#blog_headline").val();
    let slug = value
        .toLowerCase()
        .replace(/ /g, "-")
        .replace(/[^\w-]+/g, "");
    $("#blog_slug").val(slug);
}
