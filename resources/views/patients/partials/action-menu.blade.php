@php
// Ensure $patient can be used as a model or id; fall back to index route when absent
$patientRouteParam = isset($patient) ? $patient : null;
@endphp

<a href="{{ $patientRouteParam ? route('patients.show', $patientRouteParam) : route('patients.index') }}" class="menu-item" role="menuitem">
  <i class="fas fa-eye"></i>
  <span>View</span>
</a>
<a href="{{ $patientRouteParam ? route('patients.edit', $patientRouteParam) : '#' }}" class="menu-item" role="menuitem">
  <i class="fas fa-edit"></i>
  <span>Edit</span>
</a>
<form action="{{ $patientRouteParam ? route('patients.destroy', $patientRouteParam) : '#' }}" data-delete-type="patient" method="POST" class="delete-form">
  @csrf
  @method('DELETE')
  <button type="submit" class="menu-item" role="menuitem">
    <i class="fas fa-trash"></i>
    <span>Delete</span>
  </button>
</form>