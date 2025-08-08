@extends('layouts.admin')

@section('title', 'Smart Marksheet Demo')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-magic text-primary"></i>
            Smart Marksheet System Demo
        </h1>
        <div>
            <a href="{{ route('admin.marksheets.customize.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Templates
            </a>
        </div>
    </div>

    <!-- Demo Introduction -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-lightbulb"></i> How Smart Detection Works
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-success">
                <h5><i class="fas fa-check-circle"></i> Intelligent Marksheet Generation</h5>
                <p class="mb-0">
                    Our smart system automatically adapts the marksheet table based on your exam configuration. 
                    No more manual column management - the system knows what to show and what to hide!
                </p>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">✨ Smart Features:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success"></i> <strong>Dynamic Headers:</strong> Shows max marks in column headers</li>
                        <li><i class="fas fa-check text-success"></i> <strong>Auto-Hide Columns:</strong> Hides unused components automatically</li>
                        <li><i class="fas fa-check text-success"></i> <strong>Responsive Layout:</strong> Adjusts column widths intelligently</li>
                        <li><i class="fas fa-check text-success"></i> <strong>Professional Styling:</strong> Multiple table styles available</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">🎯 Benefits:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-star text-warning"></i> No technical knowledge required</li>
                        <li><i class="fas fa-star text-warning"></i> Consistent formatting across all marksheets</li>
                        <li><i class="fas fa-star text-warning"></i> Automatic adaptation to different exam types</li>
                        <li><i class="fas fa-star text-warning"></i> Professional, print-ready output</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Demo Examples -->
    <div class="row">
        <!-- Example 1: Full Exam -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-graduation-cap"></i> Full Exam (Theory + Practical + Assessment)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Exam Configuration:</small>
                        <div class="bg-light p-2 rounded">
                            <code>
                                Theory: 60 marks<br>
                                Practical: 25 marks<br>
                                Assessment: 15 marks<br>
                                Total: 100 marks
                            </code>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>Subject</th>
                                    <th>Theory(60)</th>
                                    <th>Practical(25)</th>
                                    <th>Assessment(15)</th>
                                    <th>Total(100)</th>
                                    <th>Grade</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Mathematics</strong></td>
                                    <td class="text-center">52</td>
                                    <td class="text-center">22</td>
                                    <td class="text-center">13</td>
                                    <td class="text-center bg-light"><strong>87</strong></td>
                                    <td class="text-center"><strong>A</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Science</strong></td>
                                    <td class="text-center">48</td>
                                    <td class="text-center">20</td>
                                    <td class="text-center">12</td>
                                    <td class="text-center bg-light"><strong>80</strong></td>
                                    <td class="text-center"><strong>A</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> All columns are shown because the exam has all components configured.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example 2: Theory Only -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-book"></i> Theory Only Exam
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Exam Configuration:</small>
                        <div class="bg-light p-2 rounded">
                            <code>
                                Theory: 100 marks<br>
                                Practical: <span class="text-muted">Not configured</span><br>
                                Assessment: <span class="text-muted">Not configured</span><br>
                                Total: 100 marks
                            </code>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-success text-white">
                                <tr>
                                    <th>Subject</th>
                                    <th>Theory(100)</th>
                                    <th>Total(100)</th>
                                    <th>Grade</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>English</strong></td>
                                    <td class="text-center">85</td>
                                    <td class="text-center bg-light"><strong>85</strong></td>
                                    <td class="text-center"><strong>A</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>History</strong></td>
                                    <td class="text-center">78</td>
                                    <td class="text-center bg-light"><strong>78</strong></td>
                                    <td class="text-center"><strong>B+</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-success">
                        <small><i class="fas fa-check-circle"></i> Practical and Assessment columns are automatically hidden!</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example 3: Practical Exam -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-flask"></i> Practical Exam (Theory + Practical)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Exam Configuration:</small>
                        <div class="bg-light p-2 rounded">
                            <code>
                                Theory: 70 marks<br>
                                Practical: 30 marks<br>
                                Assessment: <span class="text-muted">Not configured</span><br>
                                Total: 100 marks
                            </code>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-warning text-dark">
                                <tr>
                                    <th>Subject</th>
                                    <th>Theory(70)</th>
                                    <th>Practical(30)</th>
                                    <th>Total(100)</th>
                                    <th>Grade</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Chemistry</strong></td>
                                    <td class="text-center">58</td>
                                    <td class="text-center">26</td>
                                    <td class="text-center bg-light"><strong>84</strong></td>
                                    <td class="text-center"><strong>A</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Physics</strong></td>
                                    <td class="text-center">55</td>
                                    <td class="text-center">24</td>
                                    <td class="text-center bg-light"><strong>79</strong></td>
                                    <td class="text-center"><strong>B+</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle"></i> Assessment column is hidden, but practical is shown!</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example 4: Assessment Only -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-check"></i> Assessment Only
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Exam Configuration:</small>
                        <div class="bg-light p-2 rounded">
                            <code>
                                Theory: <span class="text-muted">Not configured</span><br>
                                Practical: <span class="text-muted">Not configured</span><br>
                                Assessment: 50 marks<br>
                                Total: 50 marks
                            </code>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>Subject</th>
                                    <th>Assessment(50)</th>
                                    <th>Total(50)</th>
                                    <th>Grade</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Project Work</strong></td>
                                    <td class="text-center">42</td>
                                    <td class="text-center bg-light"><strong>42</strong></td>
                                    <td class="text-center"><strong>A</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Presentation</strong></td>
                                    <td class="text-center">38</td>
                                    <td class="text-center bg-light"><strong>38</strong></td>
                                    <td class="text-center"><strong>B+</strong></td>
                                    <td class="text-center text-success"><strong>Pass</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Only assessment column is shown - perfect for project evaluations!</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- How to Use -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-question-circle"></i> How to Use This System
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="font-weight-bold">1</span>
                        </div>
                    </div>
                    <h6 class="text-center">Configure Your Exam</h6>
                    <p class="text-center text-muted">
                        When creating an exam, specify which components you need:
                        theory, practical, assessment, and their maximum marks.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="font-weight-bold">2</span>
                        </div>
                    </div>
                    <h6 class="text-center">Choose Template</h6>
                    <p class="text-center text-muted">
                        Select or create a marksheet template with your preferred
                        styling and layout options.
                    </p>
                </div>
                <div class="col-md-4">
                    <div class="text-center mb-3">
                        <div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span class="font-weight-bold">3</span>
                        </div>
                    </div>
                    <h6 class="text-center">Generate Marksheet</h6>
                    <p class="text-center text-muted">
                        The system automatically creates the perfect marksheet
                        layout based on your exam configuration!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
