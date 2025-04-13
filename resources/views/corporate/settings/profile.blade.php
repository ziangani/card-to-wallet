@extends('corporate.layouts.app')

@section('title', 'Company Profile')

@section('content')
<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Company Settings</h2>
    <p class="text-gray-500">Manage your company settings and preferences</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <!-- Sidebar Navigation -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-dark">Settings</h2>
            </div>
            <div class="p-4">
                <nav class="space-y-1">
                    <a href="{{ route('corporate.settings.profile') }}" class="flex items-center px-4 py-3 text-dark bg-primary bg-opacity-10 rounded-lg">
                        <i class="fas fa-building w-6 text-primary"></i>
                        <span class="font-medium">Company Profile</span>
                    </a>
                    <a href="{{ route('corporate.settings.security') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-shield-alt w-6 text-gray-500"></i>
                        <span>Security</span>
                    </a>
                    <a href="{{ route('corporate.settings.roles') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-user-tag w-6 text-gray-500"></i>
                        <span>User Roles</span>
                    </a>
                    <a href="{{ route('corporate.settings.approvals') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-check-double w-6 text-gray-500"></i>
                        <span>Approval Workflows</span>
                    </a>
                    <a href="{{ route('corporate.settings.rates') }}" class="flex items-center px-4 py-3 text-dark hover:bg-gray-50 rounded-lg transition-colors">
                        <i class="fas fa-percentage w-6 text-gray-500"></i>
                        <span>Rate Settings</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:col-span-3">
        <!-- Company Profile Form -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Company Information</h3>
        <p class="text-sm text-gray-500">Update your company details and contact information</p>
    </div>
    
    <form action="{{ route('corporate.settings.update-profile') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Company Logo -->
            <div class="w-full md:w-1/3">
                <div class="flex flex-col items-center">
                    <div class="w-40 h-40 rounded-lg bg-gray-100 flex items-center justify-center overflow-hidden mb-4">
                        @if($company->logo_path)
                            <img src="{{ $company->getLogoUrl() }}" alt="{{ $company->name }} Logo" class="w-full h-full object-cover">
                        @else
                            <div class="text-6xl text-gray-300">
                                <i class="fas fa-building"></i>
                            </div>
                        @endif
                    </div>
                    
                    <label for="logo" class="block w-full">
                        <span class="sr-only">Choose company logo</span>
                        <input type="file" id="logo" name="logo" accept="image/*" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary file:text-white
                            hover:file:bg-primary-dark">
                    </label>
                    
                    @error('logo')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                    
                    <p class="mt-2 text-xs text-gray-500">Upload a company logo (JPG, PNG, GIF, max 2MB)</p>
                </div>
            </div>
            
            <!-- Company Details -->
            <div class="w-full md:w-2/3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $company->name) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('name')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                        <input type="text" id="registration_number" name="registration_number" value="{{ old('registration_number', $company->registration_number) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('registration_number')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Tax ID</label>
                        <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id', $company->tax_id) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('tax_id')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="industry" class="block text-sm font-medium text-gray-700 mb-1">Industry</label>
                        <input type="text" id="industry" name="industry" value="{{ old('industry', $company->industry) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('industry')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $company->address) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    @error('address')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $company->city) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('city')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country', $company->country) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('country')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $company->postal_code) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('postal_code')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $company->phone_number) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('phone_number')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $company->email) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('email')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="url" id="website" name="website" value="{{ old('website', $company->website) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        @error('website')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                <i class="fas fa-save mr-2"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<!-- Company Documents -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Company Documents</h3>
        <p class="text-sm text-gray-500">Upload and manage your company documents</p>
    </div>
    
    <!-- Existing Documents -->
    <div class="p-6 border-b border-gray-200">
        <h4 class="font-medium text-gray-800 mb-4">Uploaded Documents</h4>
        
        @if($documents->isEmpty())
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No documents found</h3>
                <p class="text-gray-500">Upload company documents using the form below.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Document Type</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Document Number</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Uploaded</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($documents as $document)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @php
                                            $iconClass = 'fas fa-file-alt';
                                            if (str_contains($document->file_path, '.pdf')) {
                                                $iconClass = 'fas fa-file-pdf';
                                            } elseif (str_contains($document->file_path, '.jpg') || str_contains($document->file_path, '.jpeg') || str_contains($document->file_path, '.png')) {
                                                $iconClass = 'fas fa-file-image';
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }} text-primary mr-2"></i>
                                        <span>{{ $document->getDocumentTypeLabel() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $document->document_number ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success text-white',
                                            'rejected' => 'bg-error text-white',
                                        ];
                                        $statusClass = $statusClasses[$document->status] ?? 'bg-gray-200 text-gray-800';
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">
                                        {{ $document->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $document->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $document->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ $document->getFileUrl() }}" target="_blank" class="text-primary hover:text-primary-dark mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($document->status === 'rejected')
                                        <a href="#" onclick="document.getElementById('delete-document-{{ $document->id }}').submit(); return false;" class="text-error hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="delete-document-{{ $document->id }}" action="{{ route('corporate.settings.delete-document', $document->id) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- Upload New Document -->
    <div class="p-6">
        <h4 class="font-medium text-gray-800 mb-4">Upload New Document</h4>
        
        <form action="{{ route('corporate.settings.update-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div id="document-uploads">
                <div class="document-upload border rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="document_types[0]" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                            <select id="document_types[0]" name="document_types[0]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">Select Document Type</option>
                                <option value="certificate_of_incorporation">Certificate of Incorporation</option>
                                <option value="tax_clearance">Tax Clearance</option>
                                <option value="business_license">Business License</option>
                                <option value="company_profile">Company Profile</option>
                                <option value="director_id">Director ID</option>
                                <option value="other">Other Document</option>
                            </select>
                            @error('document_types.0')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="document_numbers[0]" class="block text-sm font-medium text-gray-700 mb-1">Document Number (Optional)</label>
                            <input type="text" id="document_numbers[0]" name="document_numbers[0]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            @error('document_numbers.0')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="documents[0]" class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
                            <input type="file" id="documents[0]" name="documents[0]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            @error('documents.0')
                                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-between">
                <button type="button" id="add-document" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-plus mr-2"></i> Add Another Document
                </button>
                
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-opacity-90">
                    <i class="fas fa-upload mr-2"></i> Upload Documents
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addDocumentButton = document.getElementById('add-document');
        const documentUploadsContainer = document.getElementById('document-uploads');
        let documentCount = 1;
        
        addDocumentButton.addEventListener('click', function() {
            const newDocumentUpload = document.createElement('div');
            newDocumentUpload.className = 'document-upload border rounded-lg p-4 mb-4';
            newDocumentUpload.innerHTML = `
                <div class="flex justify-between items-center mb-2">
                    <h5 class="font-medium text-gray-700">Additional Document</h5>
                    <button type="button" class="remove-document text-error hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="document_types[${documentCount}]" class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                        <select id="document_types[${documentCount}]" name="document_types[${documentCount}]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            <option value="">Select Document Type</option>
                            <option value="certificate_of_incorporation">Certificate of Incorporation</option>
                            <option value="tax_clearance">Tax Clearance</option>
                            <option value="business_license">Business License</option>
                            <option value="company_profile">Company Profile</option>
                            <option value="director_id">Director ID</option>
                            <option value="other">Other Document</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="document_numbers[${documentCount}]" class="block text-sm font-medium text-gray-700 mb-1">Document Number (Optional)</label>
                        <input type="text" id="document_numbers[${documentCount}]" name="document_numbers[${documentCount}]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="documents[${documentCount}]" class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
                        <input type="file" id="documents[${documentCount}]" name="documents[${documentCount}]" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                </div>
            `;
            
            documentUploadsContainer.appendChild(newDocumentUpload);
            documentCount++;
            
            // Add event listener to remove button
            const removeButton = newDocumentUpload.querySelector('.remove-document');
            removeButton.addEventListener('click', function() {
                documentUploadsContainer.removeChild(newDocumentUpload);
            });
        });
    });
</script>
@endpush
@endsection
