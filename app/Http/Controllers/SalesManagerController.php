<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalesManagerController extends Controller
{
    // Comprehensive Dummy Data
    private $criteria = [
        ['id' => 1, 'title' => 'Budget Above 10M', 'description' => 'Clients with budget over 10 million', 'type' => 'budget'],
        ['id' => 2, 'title' => 'Budget 5M-10M', 'description' => 'Clients with budget between 5-10 million', 'type' => 'budget'],
        ['id' => 3, 'title' => 'Budget Below 5M', 'description' => 'Clients with budget under 5 million', 'type' => 'budget'],
        ['id' => 4, 'title' => 'Location: Dubai', 'description' => 'Clients located in Dubai', 'type' => 'location'],
        ['id' => 5, 'title' => 'Location: Abu Dhabi', 'description' => 'Clients located in Abu Dhabi', 'type' => 'location'],
        ['id' => 6, 'title' => 'Location: Sharjah', 'description' => 'Clients located in Sharjah', 'type' => 'location'],
        ['id' => 7, 'title' => 'Property: Luxury Villa', 'description' => 'Interested in luxury villas', 'type' => 'property_type'],
        ['id' => 8, 'title' => 'Property: Apartment', 'description' => 'Interested in apartments', 'type' => 'property_type'],
        ['id' => 9, 'title' => 'Property: Commercial', 'description' => 'Interested in commercial properties', 'type' => 'property_type'],
        ['id' => 10, 'title' => 'Client: Investor', 'description' => 'Investment-focused clients', 'type' => 'client_type'],
        ['id' => 11, 'title' => 'Client: End User', 'description' => 'Clients buying for personal use', 'type' => 'client_type'],
        ['id' => 12, 'title' => 'Requirements: Urgent', 'description' => 'Clients with urgent requirements', 'type' => 'requirements'],
    ];

    private $salesPeople = [
        [
            'id' => 1, 
            'name' => 'Ahmed Al Mansoori', 
            'email' => 'ahmed.mansoori@example.com', 
            'phone' => '+971501234567',
            'description' => 'Senior Real Estate Consultant',
            'criteria_ids' => [1, 4, 7, 10], // Budget 10M+, Dubai, Luxury Villa, Investor
            'clients_count' => 8
        ],
        [
            'id' => 2, 
            'name' => 'Sarah Johnson', 
            'email' => 'sarah.j@example.com', 
            'phone' => '+971502345678',
            'description' => 'Luxury Property Specialist',
            'criteria_ids' => [1, 5, 7, 12], // Budget 10M+, Abu Dhabi, Luxury Villa, Urgent
            'clients_count' => 6
        ],
        [
            'id' => 3, 
            'name' => 'Mohammed Hassan', 
            'email' => 'm.hassan@example.com', 
            'phone' => '+971503456789',
            'description' => 'Commercial Properties Expert',
            'criteria_ids' => [2, 4, 9, 10], // Budget 5M-10M, Dubai, Commercial, Investor
            'clients_count' => 12
        ],
        [
            'id' => 4, 
            'name' => 'Fatima Al Rais', 
            'email' => 'fatima.rais@example.com', 
            'phone' => '+971504567890',
            'description' => 'First-time Buyer Specialist',
            'criteria_ids' => [3, 6, 8, 11], // Budget Below 5M, Sharjah, Apartment, End User
            'clients_count' => 15
        ],
        [
            'id' => 5, 
            'name' => 'David Chen', 
            'email' => 'd.chen@example.com', 
            'phone' => '+971505678901',
            'description' => 'International Clients Manager',
            'criteria_ids' => [1, 2, 4, 5, 10], // Multiple budgets and locations
            'clients_count' => 9
        ],
    ];

    private $clients = [
        [
            'id' => 1, 
            'name' => 'Al Futtaim Group', 
            'description' => 'Large conglomerate looking for commercial space', 
            'assigned_salesperson_id' => 3,
            'auto_assigned' => true,
            'criteria_ids' => [2, 4, 9, 10] // Matches Mohammed Hassan
        ],
        [
            'id' => 2, 
            'name' => 'Sheikh Khalid bin Ahmed', 
            'description' => 'High-net-worth individual seeking luxury villa', 
            'assigned_salesperson_id' => 1,
            'auto_assigned' => true,
            'criteria_ids' => [1, 4, 7, 10] // Matches Ahmed Al Mansoori
        ],
        [
            'id' => 3, 
            'name' => 'Tech Startup Inc', 
            'description' => 'New technology company needing office space', 
            'assigned_salesperson_id' => 3,
            'auto_assigned' => true,
            'criteria_ids' => [3, 4, 9, 12] // Matches Mohammed Hassan
        ],
        [
            'id' => 4, 
            'name' => 'The Williams Family', 
            'description' => 'Family relocating to Sharjah', 
            'assigned_salesperson_id' => 4,
            'auto_assigned' => true,
            'criteria_ids' => [3, 6, 8, 11] // Matches Fatima Al Rais
        ],
        [
            'id' => 5, 
            'name' => 'Royal Investment Group', 
            'description' => 'Investment firm with urgent requirements', 
            'assigned_salesperson_id' => 2,
            'auto_assigned' => false,
            'criteria_ids' => [1, 5, 7, 12] // Manually assigned to Sarah
        ],
    ];

    // Helper method to get criteria by IDs
    private function getCriteriaByIds($criteriaIds)
    {
        return array_filter($this->criteria, function($criterion) use ($criteriaIds) {
            return in_array($criterion['id'], $criteriaIds);
        });
    }

    // Helper method to get salesperson by ID
    private function getSalespersonById($id)
    {
        return collect($this->salesPeople)->firstWhere('id', $id);
    }

    // Helper method to get clients by salesperson ID
    private function getClientsBySalespersonId($salespersonId)
    {
        return array_filter($this->clients, function($client) use ($salespersonId) {
            return $client['assigned_salesperson_id'] == $salespersonId;
        });
    }

    // Criteria Methods
    public function criteriaIndex()
    {
        return view('criteria.index', ['criteria' => $this->criteria]);
    }

    public function criteriaCreate()
    {
        return view('criteria.create');
    }

    public function criteriaStore(Request $request)
    {
        return redirect()->route('criteria.index')
            ->with('success', 'Criteria created successfully!');
    }

    public function criteriaEdit($id)
    {
        $criterion = collect($this->criteria)->firstWhere('id', $id);
        return view('criteria.edit', compact('criterion'));
    }

    public function criteriaUpdate(Request $request, $id)
    {
        return redirect()->route('criteria.index')
            ->with('success', 'Criteria updated successfully!');
    }

    public function criteriaDestroy($id)
    {
        return redirect()->route('criteria.index')
            ->with('success', 'Criteria deleted successfully!');
    }

    // Sales Person Methods
    // In SalesManagerController.php - Update the salesPersonIndex method
    public function salesPersonIndex()
    {
        $salesPeopleWithDetails = [];
        foreach ($this->salesPeople as $person) {
            $criteria = $this->getCriteriaByIds($person['criteria_ids']);
            $clients = $this->getClientsBySalespersonId($person['id']);
            
            $salesPeopleWithDetails[] = array_merge($person, [
                'criteria' => $criteria,
                'clients' => $clients
            ]);
        }

        // Get total criteria count for stats
        $totalCriteria = count($this->criteria);

        return view('sales_person.index', [
            'salesPeople' => $salesPeopleWithDetails,
            'criteria' => $this->criteria, // Add this line
            'totalCriteria' => $totalCriteria // Add this for stats
        ]);
    }

    public function salesPersonCreate()
    {
        return view('sales_person.create', [
            'criteria' => $this->criteria,
            'salesPeople' => $this->salesPeople
        ]);
    }

    public function salesPersonStore(Request $request)
    {
        return redirect()->route('sales_person.index')
            ->with('success', 'Sales person created successfully!');
    }

    public function salesPersonShow($id)
    {
        $salesPerson = $this->getSalespersonById($id);
        $criteria = $this->getCriteriaByIds($salesPerson['criteria_ids']);
        $clients = $this->getClientsBySalespersonId($id);
        
        // Add client details
        $clientsWithDetails = [];
        foreach ($clients as $client) {
            $clientCriteria = $this->getCriteriaByIds($client['criteria_ids']);
            $clientsWithDetails[] = array_merge($client, ['criteria' => $clientCriteria]);
        }

        return view('sales_person.show', [
            'salesPerson' => $salesPerson,
            'criteria' => $criteria,
            'clients' => $clientsWithDetails
        ]);
    }

    public function salesPersonEdit($id)
    {
        $salesPerson = collect($this->salesPeople)->firstWhere('id', $id);
        return view('sales_person.edit', [
            'salesPerson' => $salesPerson,
            'criteria' => $this->criteria
        ]);
    }

    public function salesPersonUpdate(Request $request, $id)
    {
        return redirect()->route('sales_person.index')
            ->with('success', 'Sales person updated successfully!');
    }

    public function salesPersonDestroy($id)
    {
        return redirect()->route('sales_person.index')
            ->with('success', 'Sales person deleted successfully!');
    }

    // Client Methods
    public function clientIndex()
    {
        $clientsWithDetails = [];
        foreach ($this->clients as $client) {
            $salesPerson = $this->getSalespersonById($client['assigned_salesperson_id']);
            $criteria = $this->getCriteriaByIds($client['criteria_ids']);
            
            $clientsWithDetails[] = array_merge($client, [
                'salesperson_name' => $salesPerson['name'] ?? 'Not assigned',
                'salesperson' => $salesPerson,
                'criteria' => $criteria
            ]);
        }

        return view('client.index', ['clients' => $clientsWithDetails]);
    }

    public function clientCreate()
    {
        return view('client.create', [
            'criteria' => $this->criteria,
            'salesPeople' => $this->salesPeople
        ]);
    }

    public function clientStore(Request $request)
    {
        return redirect()->route('client.index')
            ->with('success', 'Client created successfully!');
    }

    public function clientShow($id)
    {
        // Just return the view with static data - no database lookup
        return view('client.show');
    }

    public function clientEdit($id)
    {
        $client = collect($this->clients)->firstWhere('id', $id);
        $clientCriteria = $this->getCriteriaByIds($client['criteria_ids']);
        
        return view('client.edit', [
            'client' => $client,
            'clientCriteria' => $clientCriteria,
            'criteria' => $this->criteria,
            'salesPeople' => $this->salesPeople
        ]);
    }

    public function clientUpdate(Request $request, $id)
    {
        return redirect()->route('client.show', $id)
            ->with('success', 'Client updated successfully!');
    }

    public function clientDestroy($id)
    {
        return redirect()->route('client.index')
            ->with('success', 'Client deleted successfully!');
    }
    public function salesPersonClients($id)
{
    $salesPerson = $this->getSalespersonById($id);
    $clients = $this->getClientsBySalespersonId($id);
    
    // Add criteria to clients
    $clientsWithDetails = [];
    foreach ($clients as $client) {
        $clientCriteria = $this->getCriteriaByIds($client['criteria_ids']);
        $clientsWithDetails[] = array_merge($client, ['criteria' => $clientCriteria]);
    }

    // Calculate stats
    $stats = [
        'total_clients' => count($clients),
        'auto_assigned' => count(array_filter($clients, function($client) {
            return $client['auto_assigned'];
        })),
        'manual_assigned' => count(array_filter($clients, function($client) {
            return !$client['auto_assigned'];
        })),
        'matching_percentage' => $this->calculateAverageMatchingPercentage($salesPerson, $clients)
    ];

    return view('sales_person.clients', [
        'salesPerson' => $salesPerson,
        'clients' => $clientsWithDetails,
        'stats' => $stats
    ]);
}

private function calculateAverageMatchingPercentage($salesPerson, $clients)
{
    if (count($clients) === 0) return 0;
    
    $totalPercentage = 0;
    foreach ($clients as $client) {
        $matchingCriteria = array_intersect($salesPerson['criteria_ids'], $client['criteria_ids']);
        $matchScore = count($matchingCriteria);
        $totalCriteria = count($client['criteria_ids']);
        $matchPercentage = $totalCriteria > 0 ? ($matchScore / $totalCriteria) * 100 : 0;
        $totalPercentage += $matchPercentage;
    }
    
    return round($totalPercentage / count($clients));
}
}