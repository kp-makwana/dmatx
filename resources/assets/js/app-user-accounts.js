/**
 * Page Account List (Same UI as User List)
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

  const dtTable = document.querySelector('.datatables-users');
  if (!dtTable) return;

  let statusObj = {
    idle: { title: 'Idle', class: 'bg-label-secondary' },
    logged_in: { title: 'Logged In', class: 'bg-label-success' },
    error: { title: 'Error', class: 'bg-label-danger' },
  };

  const dt_account = new DataTable(dtTable, {
    processing: true,
    serverSide: true,
    ajax: baseUrl + 'accounts/list',

    columns: [
      { data: 'id', visible: false },

      { data: 'client_display' },
      { data: 'status_label' },
      { data: 'token_expiry' },
      { data: 'is_active' },
      { data: 'last_login_at' },
      { data: 'id' }
    ],

    columnDefs: [

      // ACCOUNT COLUMN (avatar + client_id + name)
      {
        targets: 1,
        orderable: true,
        responsivePriority: 1,
        render: function (data, type, full) {
          return `
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-4">
                <span class="avatar-initial rounded-circle bg-label-primary">
                  ${full.client_id.substring(0, 2).toUpperCase()}
                </span>
              </div>
              <div class="d-flex flex-column">
                <span class="fw-medium">${full.client_id}</span>
                <small>${full.account_name ?? "N/A"}</small>
              </div>
            </div>
          `;
        }
      },

      // STATUS BADGE
      {
        targets: 2,
        orderable: false,
        render: (data, type, full) => {
          const status = full.status ?? 'unknown';

          const statusData = statusObj[status] || {
            class: 'bg-secondary',
            title: 'Unknown'
          };

          return `
      <span class="badge ${statusData.class}">
        ${statusData.title}
      </span>`;
        }
      },

      // ACTIVE BADGE
      {
        targets: 4,
        orderable: false,
        render: data =>
          data === 1
            ? `<span class="badge bg-label-success">Active</span>`
            : `<span class="badge bg-label-secondary">Inactive</span>`
      },

      // ACTION BUTTONS (same UI as User List)
      {
        targets: -1,
        orderable: false,
        searchable: false,
        render: (data, type, full) => `
          <div class="d-flex align-items-center">

            <a href="javascript:;"
               class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-record"
               data-id="${full.id}">
              <i class="icon-base ti tabler-trash icon-22px"></i>
            </a>

            <a href="${baseUrl}account/${full.id}"
               class="btn btn-text-secondary rounded-pill waves-effect btn-icon">
              <i class="icon-base ti tabler-eye icon-22px"></i>
            </a>

            <a href="${baseUrl}account/${full.id}/edit"
               class="btn btn-text-secondary rounded-pill waves-effect btn-icon">
              <i class="icon-base ti tabler-pencil icon-22px"></i>
            </a>

            <a href="javascript:;"
               class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
               data-bs-toggle="dropdown">
              <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-end m-0">
              <a href="${baseUrl}account/${full.id}/edit" class="dropdown-item">Edit</a>
              <a href="javascript:;" class="dropdown-item delete-record" data-id="${full.id}">Suspend</a>
            </div>

          </div>`
      }
    ],

    // DEFAULT ORDER
    order: [[1, "asc"]],

    // SAME LAYOUT AS USER LIST
    layout: {
      topStart: {
        rowClass: "row m-3 my-0 justify-content-between",
        features: [
          {
            pageLength: {
              menu: [10, 25, 50, 100],
              text: "_MENU_"
            }
          }
        ]
      },

      topEnd: {
        features: [
          {
            search: {
              placeholder: "Search Accounts",
              text: "_INPUT_"
            }
          },
          {
            buttons: [
              {
                extend: "collection",
                className: "btn btn-label-secondary dropdown-toggle",
                text:
                  '<span class="d-flex align-items-center gap-2">' +
                  '<i class="icon-base ti tabler-upload icon-xs"></i>' +
                  '<span class="d-none d-sm-inline-block">Export</span>' +
                  "</span>",
                buttons: ["print", "csv", "excel", "pdf", "copy"]
              },
              {
                text:
                  '<span class="d-flex align-items-center gap-2">' +
                  '<i class="icon-base ti tabler-plus icon-xs"></i>' +
                  '<span class="d-none d-sm-inline-block">Add Account</span>' +
                  "</span>",
                className: "add-new btn btn-primary",
                action: () => {
                  const modal = new bootstrap.Modal(document.getElementById('addAccountModal'));
                  modal.show();
                }
              }
            ]
          }
        ]
      },

      bottomStart: {
        rowClass: "row mx-3 justify-content-between",
        features: ["info"]
      },

      bottomEnd: "paging"
    },

    // Pagination icons
    language: {
      sLengthMenu: "_MENU_",
      search: "",
      searchPlaceholder: "Search Accounts",
      paginate: {
        next: '<i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>',
        previous: '<i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>',
        first: '<i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>',
        last: '<i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>'
      }
    },

    // FILTERS
    initComplete: function () {
      const api = this.api();

      const createFilter = (columnIndex, container, placeholder) => {
        const column = api.column(columnIndex);
        const select = document.createElement("select");
        select.className = "form-select text-capitalize";
        select.innerHTML = `<option value="">${placeholder}</option>`;
        document.querySelector(container).appendChild(select);

        const uniqueData = Array.from(new Set(column.data().toArray())).sort();
        uniqueData.forEach(v => {
          if (v) {
            const opt = document.createElement("option");
            opt.value = v;
            opt.textContent = v;
            select.appendChild(opt);
          }
        });

        select.addEventListener("change", () => {
          column.search(select.value).draw();
        });
      };

      createFilter(2, ".user_role", "Select Status");
      createFilter(4, ".user_status", "Select Active");
    }
  });

});

setTimeout(() => {
  const fixes = [
    { selector: '.dt-buttons .btn', classToRemove: 'btn-secondary' },
    { selector: '.dt-search .form-control', classToRemove: 'form-control-sm' },
    { selector: '.dt-length .form-select', classToRemove: 'form-select-sm', classToAdd: 'ms-0' },
    { selector: '.dt-length', classToAdd: 'mb-md-6 mb-0' },
    {
      selector: '.dt-layout-end',
      classToRemove: 'justify-content-between',
      classToAdd: 'd-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap'
    },
    { selector: '.dt-buttons', classToAdd: 'd-flex gap-4 mb-md-0 mb-4' },
    { selector: '.dt-layout-table', classToRemove: 'row mt-2' },
    { selector: '.dt-layout-full', classToRemove: 'col-md col-12', classToAdd: 'table-responsive' }
  ];

  fixes.forEach(({ selector, classToRemove, classToAdd }) => {
    document.querySelectorAll(selector).forEach(el => {
      if (classToRemove)
        classToRemove.split(" ").forEach(c => el.classList.remove(c));

      if (classToAdd)
        classToAdd.split(" ").forEach(c => el.classList.add(c));
    });
  });
}, 100);

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (() => {
    const addAccountForm = document.querySelector('#addAccountForm');

    if (addAccountForm && typeof FormValidation !== 'undefined') {
      FormValidation.formValidation(addAccountForm, {
        fields: {

          nickname: {
            validators: {
              notEmpty: { message: 'Please enter an account nickname' },
              stringLength: {
                min: 2,
                message: 'Nickname must be at least 2 characters'
              }
            }
          },

          client_id: {
            validators: {
              notEmpty: { message: 'Client ID is required' },
              stringLength: {
                min: 4,
                message: 'Client ID must be at least 4 characters'
              }
            }
          },

          api_key: {
            validators: {
              notEmpty: { message: 'API Key is required' }
            }
          },

          pin: {
            validators: {
              notEmpty: {
                message: 'PIN is required'
              },
              regexp: {
                regexp: /^[0-9]{4}$/,
                message: 'PIN must be exactly 4 digits'
              }
            }
          },

          client_secret: {
            validators: {
              notEmpty: { message: 'Client Secret is required' }
            }
          },

          totp_secret: {
            validators: {
              notEmpty: { message: 'TOTP Secret is required' },
              stringLength: {
                min: 3,
                message: 'TOTP Secret must be at least 3 characters'
              }
            }
          }

        },

        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: function (field, ele) {
              return '.col-12, .col-md-6';
            }
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },

        init: instance => {
          instance.on('plugins.message.placed', e => {
            // Fix placement inside input-groups
            if (e.element.parentElement.classList.contains('input-group')) {
              e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
            }
          });
        }
      });
    }
  })();
});
