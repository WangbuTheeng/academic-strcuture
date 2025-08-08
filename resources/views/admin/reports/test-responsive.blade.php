@extends('layouts.admin')

@section('title', 'Responsive Sub-Navbar Test')
@section('page-title', 'Responsive Test')

@section('content')
<div class="container-fluid">
    <!-- Include the responsive sub-navbar -->
    @include('admin.reports.partials.sub-navbar')
    
    <!-- Test Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">Responsive Sub-Navbar Test</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Responsive Features Test</h6>
                        <p class="mb-2">This page demonstrates the responsive sub-navbar features:</p>
                        <ul class="mb-0">
                            <li><strong>Mobile (< 576px):</strong> Collapsed menu with hamburger toggle, shortened text labels</li>
                            <li><strong>Tablet (576px - 991px):</strong> Compact navigation with abbreviated labels</li>
                            <li><strong>Desktop (992px - 1199px):</strong> Full navigation with some text optimization</li>
                            <li><strong>Large (1200px - 1399px):</strong> Full navigation with proper spacing</li>
                            <li><strong>Extra Large (≥ 1400px):</strong> Full navigation with generous spacing</li>
                        </ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Current Screen Size</h6>
                            <div id="screen-size" class="badge bg-primary fs-6"></div>
                        </div>
                        <div class="col-md-6">
                            <h6>Viewport Width</h6>
                            <div id="viewport-width" class="badge bg-success fs-6"></div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Test Instructions:</h6>
                    <ol>
                        <li>Resize your browser window to test different breakpoints</li>
                        <li>Check that the navigation collapses properly on mobile</li>
                        <li>Verify that dropdown menus position correctly</li>
                        <li>Ensure text labels adapt to screen size</li>
                        <li>Test the export dropdown functionality</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sample content to test scrolling -->
    <div class="row mt-4">
        @for($i = 1; $i <= 6; $i++)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Sample Card {{ $i }}</h6>
                    <p class="card-text">This is sample content to test the layout with the responsive navigation.</p>
                    <button class="btn btn-outline-primary btn-sm">Sample Action</button>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateScreenInfo() {
        const width = window.innerWidth;
        const screenSizeElement = document.getElementById('screen-size');
        const viewportWidthElement = document.getElementById('viewport-width');
        
        let screenSize = '';
        if (width < 576) {
            screenSize = 'Extra Small (XS)';
        } else if (width < 768) {
            screenSize = 'Small (SM)';
        } else if (width < 992) {
            screenSize = 'Medium (MD)';
        } else if (width < 1200) {
            screenSize = 'Large (LG)';
        } else if (width < 1400) {
            screenSize = 'Extra Large (XL)';
        } else {
            screenSize = 'Extra Extra Large (XXL)';
        }
        
        screenSizeElement.textContent = screenSize;
        viewportWidthElement.textContent = width + 'px';
    }
    
    // Update on load
    updateScreenInfo();
    
    // Update on resize
    window.addEventListener('resize', updateScreenInfo);
});
</script>
@endsection
