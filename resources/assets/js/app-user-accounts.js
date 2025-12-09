/**
 * Page: Account List
 */

'use strict';

document.addEventListener('DOMContentLoaded', function() {

  const dtTable = document.querySelector('.datatables-users');
  if (!dtTable) return;

  let statusObj = {
    success: { title: 'Success', class: 'bg-label-success' },
    logged_in: { title: 'Logged In', class: 'bg-label-secondary' },
    error: { title: 'Error', class: 'bg-label-danger' }
  };

  const dt_account = new DataTable(dtTable, {
    processing: true,
    serverSide: true,
    ajax: baseUrl + 'accounts/list',

    columns: [
      { data: 'id', visible: false },

      { data: 'nickname' },        // avatar + nickname HTML
      { data: 'client_display' },  // account HTML

      { data: 'status', visible: false }, // 🔹 HIDDEN RAW STATUS (success, error etc.)
      { data: 'status_label' },          // 🔹 Visible status column (badge)

      { data: 'token_expiry' },    // HTML badge
      { data: 'last_login_at' },   // formatted date
      { data: 'id' }               // actions
    ],

    columnDefs: [
      {
        targets: 2, // client_display
        orderable: true,
        responsivePriority: 1,
        render: (data) => data
      },

      // STATUS BADGE (uses full.status + config)
      {
        targets: 4, // status_label
        orderable: false,
        render: (data, type, full) => {
          const key = (full.status ?? '').toLowerCase();
          const cfg = statusObj[key] || { class: 'bg-label-secondary', title: data || 'Unknown' };

          return `<span class="badge ${cfg.class}">${cfg.title}</span>`;
        }
      },

      // TOKEN EXPIRY (HTML from API)
      {
        targets: 5,
        orderable: false,
        render: data => data
      },

      // ACTION BUTTONS
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

        </div>
      `
      }
    ],

    order: [[1, 'asc']],

    // ... layout, language etc stay same ...

    initComplete: function() {
      const api = this.api();

      const applyFilter = (colIdx, container, label) => {
        const col = api.column(colIdx);
        const select = document.createElement('select');

        select.className = 'form-select text-capitalize';
        select.innerHTML = `<option value="">${label}</option>`;
        document.querySelector(container).appendChild(select);

        // unique values from hidden status column
        [...new Set(col.data().toArray())].sort().forEach(v => {
          if (v) {
            const option = document.createElement('option');
            option.value = v;
            // Show pretty label instead of raw status (success -> Success)
            option.textContent = statusObj[v]?.title || v;
            select.appendChild(option);
          }
        });

        select.addEventListener('change', () => {
          col.search(select.value).draw();
        });
      };

      // 🔹 Use hidden status column (index 3)
      applyFilter(3, '.user_role', 'Select Status');
    }
  });

});

// Layout Fixes
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
        classToRemove.split(' ').forEach(c => el.classList.remove(c));

      if (classToAdd)
        classToAdd.split(' ').forEach(c => el.classList.add(c));
    });
  });
}, 100);
