import { useCallback, useEffect, useMemo, useState } from 'react'
import legacyDocument from './ui-template.html?raw'
import './App.css'

const API_BASE = import.meta.env.VITE_API_BASE_URL || '/api'

const endpoints = {
  clients: 'clients',
  users: 'users',
  contracts: 'contracts',
  installments: 'installments',
  products: 'products',
  customerTypes: 'customer-types',
  contractTypes: 'contract-types',
}

const labels = {
  clients: 'Cliente',
  users: 'Usuário',
  contracts: 'Contrato',
  installments: 'Parcela',
  products: 'Produto',
  customerTypes: 'Tipo de cliente',
  contractTypes: 'Tipo de contrato',
}

const roles = {
  seller: 'Vendedor',
  finance: 'Financeiro',
  admin: 'Administrador',
}

const fields = {
  clients: [
    { name: 'name', label: 'Nome / Razão Social', required: true },
    { name: 'document', label: 'CNPJ / CPF', required: true },
    { name: 'email', label: 'E-mail', type: 'email' },
    { name: 'address', label: 'Endereço' },
    { name: 'customer_type_id', label: 'Tipo de cliente', type: 'select', source: 'customerTypes' },
    { name: 'active', label: 'Ativo', type: 'checkbox' },
  ],
  users: [
    { name: 'name', label: 'Nome', required: true },
    { name: 'email', label: 'E-mail', type: 'email', required: true },
    { name: 'password', label: 'Senha', type: 'password', requiredOnCreate: true },
    { name: 'cpf', label: 'CPF' },
    { name: 'phone', label: 'Telefone' },
    { name: 'role', label: 'Cargo', type: 'select', required: true, options: [['seller', 'Vendedor'], ['finance', 'Financeiro'], ['admin', 'Administrador']] },
    { name: 'active', label: 'Ativo', type: 'checkbox' },
  ],
  products: [
    { name: 'name', label: 'Nome', required: true },
    { name: 'description', label: 'Descrição' },
    { name: 'price', label: 'Valor', type: 'number', required: true, step: '0.01' },
    { name: 'new_commission_percent', label: 'Comissão para cliente novo (%)', type: 'number', step: '0.01' },
    { name: 'existing_commission_percent', label: 'Comissão para cliente antigo (%)', type: 'number', step: '0.01' },
    { name: 'active', label: 'Ativo', type: 'checkbox' },
  ],
  customerTypes: [
    { name: 'name', label: 'Nome', required: true },
    { name: 'new_commission_percent', label: 'Comissão para cliente novo (%)', type: 'number', step: '0.01' },
    { name: 'existing_commission_percent', label: 'Comissão para cliente antigo (%)', type: 'number', step: '0.01' },
    { name: 'active', label: 'Ativo', type: 'checkbox' },
  ],
  contractTypes: [
    { name: 'name', label: 'Nome', required: true },
    { name: 'active', label: 'Ativo', type: 'checkbox' },
  ],
  contracts: [
    { name: 'client_id', label: 'Cliente', type: 'select', source: 'clients', required: true },
    { name: 'salesperson_id', label: 'Vendedor', type: 'select', source: 'users', required: true },
    { name: 'product_id', label: 'Produto', type: 'select', source: 'products' },
    { name: 'contract_type_id', label: 'Tipo de contrato', type: 'select', source: 'contractTypes' },
    { name: 'customer_type_id', label: 'Tipo de cliente', type: 'select', source: 'customerTypes' },
    { name: 'started_at', label: 'Data de início', type: 'date' },
    { name: 'ended_at', label: 'Data de término', type: 'date' },
    { name: 'hours_worked', label: 'Horas trabalhadas', type: 'number' },
    { name: 'contact_name', label: 'Nome do contato' },
    { name: 'contact_phone', label: 'Telefone do contato' },
    { name: 'contact_email', label: 'E-mail do contato', type: 'email' },
    { name: 'entry_amount', label: 'Valor da entrada', type: 'number', step: '0.01' },
    { name: 'total_amount', label: 'Valor total', type: 'number', required: true, step: '0.01' },
    { name: 'payment_method', label: 'Pagamento', type: 'select', options: [['cash', 'À vista'], ['installments', 'Parcelado']] },
    { name: 'installments_count', label: 'Quantidade de parcelas', type: 'number' },
    { name: 'status', label: 'Status', type: 'select', options: [['active', 'Ativo'], ['inactive', 'Inativo'], ['completed', 'Concluído'], ['cancelled', 'Cancelado']] },
  ],
  installments: [
    { name: 'contract_id', label: 'Contrato', type: 'select', source: 'contracts', required: true },
    { name: 'number', label: 'Número da parcela', type: 'number', required: true },
    { name: 'due_date', label: 'Vencimento', type: 'date', required: true },
    { name: 'amount', label: 'Valor', type: 'number', required: true, step: '0.01' },
    { name: 'commission_amount', label: 'Comissão', type: 'number', step: '0.01' },
    { name: 'status', label: 'Status', type: 'select', options: [['pending', 'Pendente'], ['paid', 'Paga'], ['cancelled', 'Cancelada']] },
  ],
}

function escapeHtml(value = '') {
  return String(value).replace(/[&<>"']/g, (character) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[character])
}

function money(value) {
  return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
}

function statusLabel(status) {
  return {
    active: 'Ativo',
    inactive: 'Inativo',
    completed: 'Concluído',
    cancelled: 'Cancelado',
  }[status] || status || 'Ativo'
}

function contractNumber(contract) {
  return `#${1200 + Number(contract.id)}`
}

function commissionTotal(contract) {
  return (contract.installments || []).reduce((total, installment) => {
    return total + Number(installment.commission_amount || 0)
  }, 0)
}

function recordName(resource, record) {
  if (resource === 'contracts') return `Contrato ${contractNumber(record)}`
  if (resource === 'installments') return `Parcela ${record.number}`
  return record.name || record.email || `#${record.id}`
}

function formatDate(value) {
  if (!value) return '—'
  const [year, month, day] = String(value).slice(0, 10).split('-')
  return `${day}/${month}/${year}`
}

function tableActionButtons(resource, record) {
  return `<span class="inline-flex items-center gap-2 whitespace-nowrap">
    <button type="button" data-edit-resource="${resource}" data-record-id="${record.id}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Editar</button>
    <button type="button" data-delete-resource="${resource}" data-record-id="${record.id}" class="text-red-500 hover:text-red-700 font-medium text-xs">Excluir</button>
  </span>`
}

function makeDocument(records) {
  const parsed = new DOMParser().parseFromString(legacyDocument, 'text/html')

  parsed.querySelectorAll('script').forEach((script) => {
    if (script.textContent.includes('document.write')) {
      let output = ''
      new Function('document', script.textContent)({
        write: (html) => {
          output += html
        },
      })
      script.outerHTML = output
    } else {
      script.remove()
    }
  })

  const clients = records.clients || []
  const users = records.users || []
  const contracts = records.contracts || []
  const products = records.products || []
  const customerTypes = records.customerTypes || []
  const contractTypes = records.contractTypes || []
  const activeContracts = [...contracts].sort((a, b) => b.id - a.id)

  const clientCards = clients.map((client) => `
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center">
      <div>
        <h4 class="font-bold text-slate-800">${escapeHtml(client.name)}</h4>
        <p class="text-xs text-slate-400">${escapeHtml(client.document)}</p>
        <span class="inline-block mt-2 text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded">${escapeHtml(client.customer_type?.name || 'Cliente')}</span>
      </div>
      <div class="flex items-center gap-2">
        <button type="button" data-edit-resource="clients" data-record-id="${client.id}" class="text-slate-400 hover:text-blue-600 p-2" aria-label="Editar cliente"><i class="fa-solid fa-pen"></i></button>
        <button type="button" data-delete-resource="clients" data-record-id="${client.id}" class="text-slate-400 hover:text-red-600 p-2" aria-label="Excluir cliente"><i class="fa-solid fa-trash"></i></button>
      </div>
    </div>`).join('')

  const userCards = users.map((user) => `
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex flex-col justify-between hover:shadow-md transition">
      <div class="flex items-start justify-between">
        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700">${escapeHtml(user.name.slice(0, 1))}</div>
        <span class="text-xs px-2.5 py-1 rounded-full font-semibold bg-green-100 text-green-700">${escapeHtml(roles[user.role] || user.role)}</span>
      </div>
      <div class="mt-4">
        <h4 class="font-bold text-slate-800">${escapeHtml(user.name)}</h4>
        <p class="text-xs text-slate-400 truncate">${escapeHtml(user.email)}</p>
      </div>
      <div class="mt-3 flex justify-end gap-3">
        <button type="button" data-edit-resource="users" data-record-id="${user.id}" class="text-blue-600 hover:text-blue-800 text-xs">Editar</button>
        <button type="button" data-delete-resource="users" data-record-id="${user.id}" class="text-red-500 hover:text-red-700 text-xs">Excluir</button>
      </div>
    </div>`).join('')

  const contractRows = activeContracts.map((contract) => `
    <tr class="hover:bg-slate-50 transition">
      <td class="p-4 font-semibold text-slate-800">${escapeHtml(contractNumber(contract))}</td>
      <td class="p-4">${escapeHtml(formatDate(contract.started_at))}</td>
      <td class="p-4 font-medium text-slate-700">${escapeHtml(contract.client?.name || '—')}</td>
      <td class="p-4"><span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs">${escapeHtml(contract.customer_type?.name || '—')}</span></td>
      <td class="p-4">${escapeHtml(contract.salesperson?.name || '—')}</td>
      <td class="p-4 font-semibold text-slate-800">${escapeHtml(money(contract.total_amount))}</td>
      <td class="p-4"><span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-semibold">${escapeHtml(statusLabel(contract.status))}</span></td>
      <td class="p-4 text-right">${tableActionButtons('contracts', contract)}</td>
    </tr>`).join('')

  const commissionRows = activeContracts.map((contract) => `
    <tr class="hover:bg-slate-50 transition">
      <td class="p-4 font-bold text-blue-600">${escapeHtml(contractNumber(contract))}</td>
      <td class="p-4">${escapeHtml(formatDate(contract.started_at))}</td>
      <td class="p-4 font-medium text-slate-700">${escapeHtml(contract.client?.name || '—')}</td>
      <td class="p-4"><span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-xs">${escapeHtml(contract.customer_type?.name || '—')}</span></td>
      <td class="p-4 font-medium">${escapeHtml(`${contract.installments_count || 1}X`)}</td>
      <td class="p-4 font-semibold text-slate-800">${escapeHtml(money(contract.total_amount))}</td>
      <td class="p-4 font-bold text-green-600">${escapeHtml(money(commissionTotal(contract)))}</td>
      <td class="p-4 text-right">${tableActionButtons('contracts', contract)}</td>
    </tr>`).join('')

  const latestRows = activeContracts.slice(0, 5).map((contract) => `
    <tr class="hover:bg-slate-50 transition">
      <td class="p-3 font-medium text-slate-800">${escapeHtml(contractNumber(contract))}</td>
      <td class="p-3">${escapeHtml(formatDate(contract.started_at))}</td>
      <td class="p-3">${escapeHtml(contract.client?.name || '—')}</td>
      <td class="p-3"><span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-semibold">${escapeHtml(statusLabel(contract.status))}</span></td>
      <td class="p-3 text-right">${tableActionButtons('contracts', contract)}</td>
    </tr>`).join('')

  const clientGrid = parsed.querySelector('#screen-cadastros-list .grid')
  if (clientGrid) clientGrid.innerHTML = clientCards || '<p class="text-sm text-slate-500">Nenhum cliente cadastrado.</p>'

  const teamGrid = parsed.querySelector('#screen-financeiro-usuarios .grid')
  if (teamGrid) teamGrid.innerHTML = userCards || '<p class="text-sm text-slate-500">Nenhum usuário cadastrado.</p>'

  const reportBody = parsed.querySelector('#screen-relatorio tbody')
  if (reportBody) reportBody.innerHTML = contractRows || '<tr><td class="p-4 text-slate-500" colspan="8">Nenhum contrato cadastrado.</td></tr>'

  const commissionsBody = parsed.querySelector('#screen-comissoes tbody')
  if (commissionsBody) commissionsBody.innerHTML = commissionRows || '<tr><td class="p-4 text-slate-500" colspan="8">Nenhuma comissão registrada.</td></tr>'

  const commissionsHeading = parsed.querySelector('#screen-comissoes h2')
  if (commissionsHeading) {
    const button = parsed.createElement('button')
    button.type = 'button'
    button.dataset.createResource = 'installments'
    button.className = 'mt-3 rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-900'
    button.textContent = '+ Nova Parcela'
    commissionsHeading.parentElement.append(button)
  }

  const latestBody = parsed.querySelector('#screen-home tbody')
  if (latestBody) latestBody.innerHTML = latestRows || '<tr><td class="p-3 text-slate-500" colspan="5">Nenhuma venda cadastrada.</td></tr>'

  const totalCommission = contracts.reduce((total, contract) => total + commissionTotal(contract), 0)
  const homeRemuneration = parsed.querySelector('#screen-home .bg-gradient-to-r h2')
  if (homeRemuneration) homeRemuneration.textContent = money(totalCommission)
  const commissionsRemuneration = parsed.querySelector('#screen-comissoes > div:first-child > div span:last-child')
  if (commissionsRemuneration) commissionsRemuneration.textContent = money(totalCommission)

  const productCard = parsed.querySelector('#screen-ajustes .grid > div:nth-child(3) .space-y-3')
  if (productCard) {
    productCard.innerHTML = products.map((product) => `
      <div class="p-3 border border-slate-100 rounded-xl bg-slate-50 flex justify-between items-center gap-3">
        <div>
          <p class="font-semibold text-sm text-slate-800">${escapeHtml(product.name)}</p>
          <p class="text-xs text-slate-500">Novo: ${escapeHtml(product.new_commission_percent)}% | Antigo: ${escapeHtml(product.existing_commission_percent)}%</p>
        </div>
        <div class="flex items-center gap-3">
          <span class="font-bold text-sm text-blue-600">${escapeHtml(money(product.price))}</span>
          <button type="button" data-edit-resource="products" data-record-id="${product.id}" class="text-blue-600 text-xs">Editar</button>
          <button type="button" data-delete-resource="products" data-record-id="${product.id}" class="text-red-500 text-xs">Excluir</button>
        </div>
      </div>`).join('') || '<p class="text-sm text-slate-500">Nenhum produto cadastrado.</p>'
  }

  const typeLists = [
    { selector: '#screen-ajustes .grid > div:nth-child(1) .space-y-2', rows: customerTypes },
    { selector: '#screen-ajustes .grid > div:nth-child(2) .space-y-2', rows: contractTypes },
  ]
  typeLists.forEach(({ selector, rows }) => {
    const list = parsed.querySelector(selector)
    if (!list) return
    list.innerHTML = rows.map((record) => `
      <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg text-sm">
        <span>${escapeHtml(record.name)}</span>
        <span class="flex items-center gap-3">
          <button type="button" data-edit-resource="${selector.includes('nth-child(1)') ? 'customerTypes' : 'contractTypes'}" data-record-id="${record.id}" class="text-blue-600 text-xs">Editar</button>
          <button type="button" data-delete-resource="${selector.includes('nth-child(1)') ? 'customerTypes' : 'contractTypes'}" data-record-id="${record.id}" class="text-red-500 text-xs">Excluir</button>
        </span>
      </div>`).join('') || '<p class="text-sm text-slate-500">Nenhum tipo cadastrado.</p>'
  })

  parsed.querySelectorAll('#screen-ajustes button[onclick*="modal-novo-tipo-cliente"]').forEach((button) => {
    button.dataset.createResource = 'customerTypes'
    button.removeAttribute('onclick')
  })
  parsed.querySelectorAll('#screen-ajustes button[onclick*="modal-novo-tipo-contrato"]').forEach((button) => {
    button.dataset.createResource = 'contractTypes'
    button.removeAttribute('onclick')
  })
  parsed.querySelectorAll('#screen-ajustes button[onclick*="modal-novo-produto"]').forEach((button) => {
    button.dataset.createResource = 'products'
    button.removeAttribute('onclick')
  })

  const reportHeading = parsed.querySelector('#screen-relatorio h2')
  if (reportHeading) {
    const button = parsed.createElement('button')
    button.type = 'button'
    button.dataset.createResource = 'contracts'
    button.className = 'bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow transition'
    button.textContent = '+ Novo Contrato'
    reportHeading.parentElement.append(button)
  }

  parsed.querySelectorAll('#screen-cadastros-list button[onclick*="screen-cadastro-cliente"]').forEach((button) => {
    button.dataset.createResource = 'clients'
    button.removeAttribute('onclick')
  })
  parsed.querySelectorAll('#screen-cadastros-list button[onclick*="screen-cadastro-usuario"]').forEach((button) => {
    button.dataset.createResource = 'users'
    button.removeAttribute('onclick')
  })

  const clientForm = parsed.querySelector('#screen-cadastro-cliente form')
  if (clientForm) {
    clientForm.dataset.apiResource = 'clients'
    ;['name', 'document', 'email', 'address'].forEach((name, index) => {
      const input = clientForm.querySelectorAll('input')[index]
      if (input) input.name = name
    })
  }

  const userForm = parsed.querySelector('#screen-cadastro-usuario form')
  if (userForm) {
    userForm.dataset.apiResource = 'users'
    ;['name', 'cpf', 'email', 'password'].forEach((name, index) => {
      const input = userForm.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]')[index]
      if (input) input.name = name
    })
    userForm.querySelectorAll('input[type="radio"]').forEach((radio, index) => {
      radio.name = 'role'
      radio.value = ['seller', 'finance', 'admin'][index]
    })
  }

  const contractForm = parsed.querySelector('#screen-venda-detalhes form')
  if (contractForm) {
    contractForm.dataset.apiResource = 'contracts'
    const contractInputs = [
      ['Data de Início', 'started_at'],
      ['Data de Término', 'ended_at'],
      ['Horas trabalhadas', 'hours_worked'],
      ['Nome Contato', 'contact_name'],
      ['Telefone', 'contact_phone'],
      ['E-mail', 'contact_email'],
      ['Valor da Entrada', 'entry_amount'],
    ]
    contractInputs.forEach(([label, name]) => {
      const field = [...contractForm.querySelectorAll('label')].find((item) => item.textContent.trim() === label)?.parentElement?.querySelector('input,select,textarea')
      if (field) field.name = name
    })

    const customerSelect = [...contractForm.querySelectorAll('label')].find((item) => item.textContent.trim() === 'Cliente')?.parentElement?.querySelector('input')
    const salespersonSelect = [...contractForm.querySelectorAll('label')].find((item) => item.textContent.trim() === 'Vendedor')?.parentElement?.querySelector('input')
    const replaceWithSelect = (input, name, options) => {
      if (!input) return
      const select = parsed.createElement('select')
      select.name = name
      select.required = true
      select.className = input.className.replace('border-b', 'border rounded-lg px-2')
      select.innerHTML = `<option value="">Selecione</option>${options.map(([value, label]) => `<option value="${escapeHtml(value)}">${escapeHtml(label)}</option>`).join('')}`
      input.replaceWith(select)
    }
    replaceWithSelect(customerSelect, 'client_id', clients.map((item) => [item.id, item.name]))
    replaceWithSelect(salespersonSelect, 'salesperson_id', users.filter((item) => item.role === 'seller').map((item) => [item.id, item.name]))

    const productSelect = contractForm.querySelector('select')
    if (productSelect) {
      productSelect.name = 'product_id'
      productSelect.innerHTML = `<option value="">Selecione</option>${products.map((item) => `<option value="${item.id}">${escapeHtml(item.name)}</option>`).join('')}`
    }
    const clientTypeSelect = [...contractForm.querySelectorAll('label')].find((item) => item.textContent.trim() === 'Status Cliente')?.parentElement?.querySelector('select')
    if (clientTypeSelect) {
      clientTypeSelect.name = 'customer_type_id'
      clientTypeSelect.innerHTML = `<option value="">Selecione</option>${customerTypes.map((item) => `<option value="${item.id}">${escapeHtml(item.name)}</option>`).join('')}`
    }
    contractForm.querySelectorAll('input[type="radio"][name="st_c"]').forEach((radio, index) => {
      radio.name = 'status'
      radio.value = index === 0 ? 'active' : 'inactive'
    })
    contractForm.querySelectorAll('input[type="radio"][name="metodo"]').forEach((radio, index) => {
      radio.name = 'payment_method'
      radio.value = index === 0 ? 'cash' : 'installments'
    })
    const installmentsInput = parsed.createElement('input')
    installmentsInput.type = 'hidden'
    installmentsInput.name = 'installments_count'
    installmentsInput.value = '36'
    contractForm.append(installmentsInput)
  }

  return parsed.body.innerHTML
}

async function request(path, options = {}) {
  const response = await fetch(`${API_BASE}/${path}`, {
    ...options,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...options.headers,
    },
  })

  if (!response.ok) {
    const result = await response.json().catch(() => ({}))
    const validationMessage = Object.values(result.errors || {}).flat()[0]
    throw new Error(validationMessage || result.message || `A API respondeu com erro ${response.status}.`)
  }

  if (response.status === 204) return null
  return response.json()
}

function selectOptions(field, records) {
  if (field.options) return field.options
  const source = records[field.source] || []
  return source.map((record) => [
    record.id,
    field.source === 'contracts' ? contractNumber(record) : field.source === 'users' ? record.name : record.name,
  ])
}

function normalizePayload(resource, form) {
  const payload = Object.fromEntries(new FormData(form).entries())
  form.querySelectorAll('input[type="checkbox"][name]').forEach((input) => {
    payload[input.name] = input.checked
  })
  const numericFields = new Set([
    'client_id', 'salesperson_id', 'product_id', 'contract_type_id', 'customer_type_id',
    'hours_worked', 'entry_amount', 'total_amount', 'installments_count', 'number',
    'amount', 'commission_amount', 'price', 'new_commission_percent',
    'existing_commission_percent',
  ])

  Object.entries(payload).forEach(([key, value]) => {
    if (value === '') {
      delete payload[key]
    } else if (numericFields.has(key)) {
      payload[key] = Number(value)
    }
  })

  if (resource === 'users' && form.dataset.create === 'true' && !payload.password) {
    throw new Error('Informe uma senha com pelo menos 8 caracteres.')
  }

  return payload
}

function ResourceForm({ editor, records, onClose, onSave, saving }) {
  const { resource, record } = editor

  return (
    <div className="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4" role="presentation" onMouseDown={(event) => {
      if (event.target === event.currentTarget) onClose()
    }}>
      <section className="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="resource-form-title">
        <div className="mb-5 flex items-center justify-between">
          <h2 id="resource-form-title" className="text-xl font-bold text-slate-800">{record ? 'Editar' : 'Cadastrar'} {labels[resource]}</h2>
          <button type="button" onClick={onClose} className="rounded-lg px-3 py-1 text-slate-500 hover:bg-slate-100" aria-label="Fechar">✕</button>
        </div>
        <form className="grid grid-cols-1 gap-4 sm:grid-cols-2" onSubmit={(event) => {
          event.preventDefault()
          onSave(event.currentTarget)
        }} data-create={!record}>
          {fields[resource].map((field) => {
            const value = record?.[field.name] ?? (resource === 'users' && field.name === 'role' ? 'seller' : '')
            const required = field.required || (field.requiredOnCreate && !record)
            if (field.type === 'checkbox') {
              return (
                <label key={field.name} className="flex items-center gap-2 py-2 text-sm text-slate-700">
                  <input type="checkbox" name={field.name} defaultChecked={record ? Boolean(value) : true} className="rounded border-slate-300 text-blue-600" />
                  {field.label}
                </label>
              )
            }
            if (field.type === 'select') {
              return (
                <label key={field.name} className="block text-xs font-medium text-slate-600">
                  {field.label}
                  <select name={field.name} defaultValue={value} required={required} className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500">
                    <option value="">Selecione</option>
                    {selectOptions(field, records).map(([optionValue, optionLabel]) => (
                      <option key={optionValue} value={optionValue}>{optionLabel}</option>
                    ))}
                  </select>
                </label>
              )
            }
            return (
              <label key={field.name} className="block text-xs font-medium text-slate-600">
                {field.label}
                <input
                  type={field.type || 'text'}
                  name={field.name}
                  defaultValue={field.type === 'password' ? '' : value}
                  required={required}
                  step={field.step}
                  min={field.type === 'number' ? '0' : undefined}
                  autoComplete={field.name === 'password' ? 'new-password' : undefined}
                  className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-blue-500"
                />
              </label>
            )
          })}
          <div className="col-span-full flex justify-end gap-3 pt-2">
            <button type="button" onClick={onClose} className="rounded-xl bg-slate-100 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200">Cancelar</button>
            <button type="submit" disabled={saving} className="rounded-xl bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
              {saving ? 'Salvando...' : 'Salvar'}
            </button>
          </div>
        </form>
      </section>
    </div>
  )
}

function App() {
  const [records, setRecords] = useState({})
  const [editor, setEditor] = useState(null)
  const [toast, setToast] = useState('')
  const [saving, setSaving] = useState(false)
  const [activeScreen, setActiveScreen] = useState('screen-login')
  const markup = useMemo(() => makeDocument(records), [records])

  const refresh = useCallback(async () => {
    const entries = await Promise.all(Object.entries(endpoints).map(async ([resource, endpoint]) => {
      const result = await request(endpoint)
      return [resource, result.data || result]
    }))
    setRecords(Object.fromEntries(entries))
  }, [])

  const showLoadError = useCallback((error) => {
    setToast(`Não foi possível carregar os dados: ${error.message}`)
  }, [])

  useEffect(() => {
    let active = true
    Promise.resolve()
      .then(refresh)
      .catch((error) => {
        if (active) showLoadError(error)
      })

    return () => {
      active = false
    }
  }, [refresh, showLoadError])

  useEffect(() => {
    const applyScreen = (screenId) => {
      const loginScreen = screenId === 'screen-login' || screenId === 'screen-forgot-password'
      document.getElementById('app-layout')?.classList.toggle('hidden', loginScreen)
      document.getElementById('screen-login')?.classList.toggle('hidden', screenId !== 'screen-login')
      document.getElementById('screen-forgot-password')?.classList.toggle('hidden', screenId !== 'screen-forgot-password')
      document.querySelectorAll('.app-screen').forEach((screen) => {
        screen.classList.toggle('hidden', screen.id !== screenId)
      })
      window.scrollTo(0, 0)
    }
    window.navigateTo = (screenId) => {
      if (screenId !== activeScreen) setActiveScreen(screenId)
      applyScreen(screenId)
    }
    window.toggleSidebar = () => document.getElementById('sidebar')?.classList.toggle('-translate-x-full')
    window.showModal = (modalId) => document.getElementById(modalId)?.classList.remove('hidden')
    window.closeModal = (modalId) => document.getElementById(modalId)?.classList.add('hidden')
    applyScreen(activeScreen)

    const onClick = (event) => {
      const target = event.target.closest('[data-create-resource], [data-edit-resource], [data-delete-resource]')
      if (!target) return
      event.preventDefault()
      event.stopImmediatePropagation()

      const resource = target.dataset.createResource || target.dataset.editResource || target.dataset.deleteResource
      const recordId = target.dataset.recordId
      if (target.dataset.createResource) {
        setEditor({ resource, record: null })
      } else if (target.dataset.editResource) {
        const record = (records[resource] || []).find((item) => String(item.id) === recordId)
        if (record) setEditor({ resource, record })
      } else {
        const record = (records[resource] || []).find((item) => String(item.id) === recordId)
        if (record && window.confirm(`Excluir ${labels[resource].toLowerCase()} "${recordName(resource, record)}"?`)) {
          request(`${endpoints[resource]}/${recordId}`, { method: 'DELETE' })
            .then(() => refresh())
            .then(() => setToast(`${labels[resource]} excluído(a) com sucesso.`))
            .catch((error) => setToast(`Não foi possível excluir: ${error.message}`))
        }
      }
    }

    const onSubmit = (event) => {
      const form = event.target.closest('form[data-api-resource]')
      if (!form) return
      event.preventDefault()
      event.stopImmediatePropagation()

      const resource = form.dataset.apiResource
      const payload = Object.fromEntries(new FormData(form).entries())
      if (resource === 'contracts') {
        const amount = Number(String(payload.entry_amount || '').replace(/\D/g, '')) / 100
        payload.entry_amount = amount
        payload.total_amount = amount
      }
      if (resource === 'users') {
        const selectedRole = form.querySelector('input[name="role"]:checked')
        if (selectedRole) payload.role = selectedRole.value
      }

      setSaving(true)
      request(endpoints[resource], { method: 'POST', body: JSON.stringify(payload) })
        .then(() => refresh())
        .then(() => {
          setToast(`${labels[resource]} cadastrado(a) com sucesso.`)
          window.navigateTo(resource === 'contracts' ? 'screen-relatorio' : 'screen-cadastros-list')
        })
        .catch((error) => setToast(`Não foi possível salvar: ${error.message}`))
        .finally(() => setSaving(false))
    }

    document.addEventListener('click', onClick, true)
    document.addEventListener('submit', onSubmit, true)
    return () => {
      document.removeEventListener('click', onClick, true)
      document.removeEventListener('submit', onSubmit, true)
      delete window.navigateTo
      delete window.toggleSidebar
      delete window.showModal
      delete window.closeModal
    }
  }, [activeScreen, records, refresh])

  const saveRecord = async (form) => {
    setSaving(true)
    try {
      const payload = normalizePayload(editor.resource, form)
      const endpoint = endpoints[editor.resource]
      const path = editor.record ? `${endpoint}/${editor.record.id}` : endpoint
      await request(path, {
        method: editor.record ? 'PUT' : 'POST',
        body: JSON.stringify(payload),
      })
      await refresh()
      setToast(`${labels[editor.resource]} ${editor.record ? 'atualizado(a)' : 'cadastrado(a)'} com sucesso.`)
      setEditor(null)
    } catch (error) {
      setToast(`Não foi possível salvar: ${error.message}`)
    } finally {
      setSaving(false)
    }
  }

  return (
    <>
      <div dangerouslySetInnerHTML={{ __html: markup }} />
      {toast && (
        <div role="status" className="fixed bottom-4 right-4 z-[110] max-w-md rounded-xl bg-slate-900 px-4 py-3 text-sm text-white shadow-xl">
          <div className="flex items-start justify-between gap-4">
            <span>{toast}</span>
            <button type="button" onClick={() => setToast('')} className="font-bold text-slate-300 hover:text-white" aria-label="Fechar aviso">×</button>
          </div>
        </div>
      )}
      {editor && (
        <ResourceForm
          editor={editor}
          records={records}
          onClose={() => setEditor(null)}
          onSave={saveRecord}
          saving={saving}
        />
      )}
    </>
  )
}

export default App
