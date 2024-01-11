<style>
    button.dt-button.buttons-csv.buttons-html5 {
        margin-left: 12px;
        padding: 5px 12px;
        font-weight: 400;
        background: #95D870;
        color: white;
        border: 1px solid  #EDF1F5;
        border-radius: 6px;
    }
    button.dt-button.buttons-csv.buttons-html5:hover {
        margin-left: 12px;
        padding: 5px 12px;
        font-weight: 400;
        background: #a1e67d;
        color: white;
        border: 1px solid  #95D870;
        border-radius: 6px;
    }

    .select2-selection__arrow {
        display: none;
    }

    tr td {
        color: black !important;
    }

    .tr_header {
        background-color: #EDF1F5;
    }

    table.dataTable thead th,
    table.dataTable thead td {
        padding: 10px 18px;
        border-bottom: 1px solid #e4e7ea;
    }

    .validateRq {
        color: red;
    }

    .dropdown-menu>li>a {
        padding: 2px 20px !important;
    }

    .custom-file-upload {
        color: grey !important;
        display: inline-block;
        padding: 4px 4px 4px 4px;
        cursor: pointer;
        font-weight: normal;
        width: 600px;
        height: 32px;

    }

    input::file-selector-button {
        display: inline-block;
        font-weight: bolder;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        /* background: #064420; */
        background: #41b3f9;
        border-width: 1px;
        border: none;
        font-size: 12px;
        overflow: hidden;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        background-size: 12px 12px;
        padding: 4px 4px 4px 4px;
    }

    .scrollable-menu {
        height: auto;
        max-height: 200px;
        overflow-x: hidden;
    }

    .btnColor {
        color: #fff !important;
    }

    .panel .panel-heading {
        border-radius: 0;
        font-weight: 500;
        font-size: 13px;
        padding: 10px 25px;
        border: none'

    }

    .center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 60%;
        height: 100%;
    }

    .fade1 {
        background-color: white;
        opacity: 0.1;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        margin: 0px;
        width: 100%;
        height: auto;
        position: fixed;
        z-index: 1040;
        display: none;
    }

    .fade1 .spin {
        position: absolute;
        top: 48%;
        left: 48%;
    }

    /*for yellow bg*/

    .bg-title .breadcrumb {
        background: 0 0;
        margin-bottom: 0;
        float: none;
        padding: 0;
        margin-bottom: 9px;
        font-weight: 700;
        color: #777;
    }


    .select2-container .select2-selection--single .select2-selection__rendered {
        height: auto;
        margin-top: -6px;
        padding-left: 0;
        padding-right: 0;
    }

    .select2-container .select2-selection--single {
        box-sizing: border-box;
        cursor: pointer;
        display: block;
        height: 35px;
    }

    .select2-container--default .select2-selection--single,
    .select2-selection .select2-selection--single {
        border: 1px solid #d2d6de;
        border-radius: 0;
        padding: 8px 11px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px;
        position: absolute;
        top: 4px;
        right: 1px;
        width: 20px;
    }


    .btn_style {
        width: 106px;
    }

    .error {
        color: red;
    }



    @-webkit-keyframes stripMove {
        0% {
            transform: translate3d(0px, 0px, 0px);
            -webkit-transform: translate3d(0px, 0px, 0px);
            -moz-transform: translate3d(0px, 0px, 0px);
        }

        50% {
            transform: translate3d(0px, 0px, 0px);
            -webkit-transform: translate3d(0px, 0px, 0px);
            -moz-transform: translate3d(0px, 0px, 0px);
            transform: scale(4, 1);
            -webkit-transform: scale(4, 1);
            -moz-transform: scale(4, 1);
        }

        100% {
            transform: translate3d(-50px, 0px, 0px);
            -webkit-transform: translate3d(-50px, 0px, 0px);
            -moz-transform: translate3d(-50px, 0px, 0px);
        }
    }

    @-moz-keyframes stripMove {
        0% {
            transform: translate3d(-50px, 0px, 0px);
            -webkit-transform: translate3d(-50px, 0px, 0px);
            -moz-transform: translate3d(-50px, 0px, 0px);
        }

        50% {
            transform: translate3d(0px, 0px, 0px);
            -webkit-transform: translate3d(0px, 0px, 0px);
            -moz-transform: translate3d(0px, 0px, 0px);
            transform: scale(4, 1);
            -webkit-transform: scale(4, 1);
            -moz-transform: scale(4, 1);
        }

        100% {
            transform: translate3d(50px, 0px, 0px);
            -webkit-transform: translate3d(50px, 0px, 0px);
            -moz-transform: translate3d(50px, 0px, 0px);
        }
    }

    @keyframes stripMove {
        0% {
            transform: translate3d(-50px, 0px, 0px);
            -webkit-transform: translate3d(-50px, 0px, 0px);
            -moz-transform: translate3d(-50px, 0px, 0px);
        }

        50% {
            transform: translate3d(0px, 0px, 0px);
            -webkit-transform: translate3d(0px, 0px, 0px);
            -moz-transform: translate3d(0px, 0px, 0px);
            transform: scale(4, 1);
            -webkit-transform: scale(4, 1);
            -moz-transform: scale(4, 1);
        }

        100% {
            transform: translate3d(50px, 0px, 0px);
            -webkit-transform: translate3d(50px, 0px, 0px);
            -moz-transform: translate3d(50px, 0px, 0px);
        }
    }
</style>
