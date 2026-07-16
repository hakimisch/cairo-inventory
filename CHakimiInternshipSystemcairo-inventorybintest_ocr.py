import sys, subprocess, json, os

def run_ocr(pdf_name):
    """Test the OCR pipeline on a given PDF file."""
    base = r"C:\Hakimi\Internship\System\cairo-inventory\storage\app\item_documents"
    pdf_path = os.path.join(base, pdf_name)
    script = r"C:\Hakimi\Internship\System\cairo-inventory\bin\cairo-ocr"
    
    result = subprocess.run(
        [sys.executable, script, pdf_path, "--format", "json"],
        capture_output=True, text=True, timeout=120
    )
    
    print(f"=== {pdf_name} ===")
    print(f"Return code: {result.returncode}")
    if result.stderr:
        print(f"STDERR: {result.stderr[:500]}")
    if result.stdout:
        try:
            data = json.loads(result.stdout)
            items = data.get("items", [])
            print(f"Items extracted: {len(items)}")
            print(f"Document type: {data.get('document_type', 'N/A')}")
            print(f"Reference: {data.get('reference', 'N/A')}")
            if items:
                for i, item in enumerate(items[:3]):
                    print(f"  [{i+1}] {item.get('description','?')[:60]} | qty={item.get('quantity_ordered')} | price={item.get('unit_price')}")
        except json.JSONDecodeError:
            print(f"STDOUT (first 800 chars): {result.stdout[:800]}")
    print()

# Test both PDFs
run_ocr("Acknowledment Goods DOTCOM.pdf")
run_ocr("JADUAL HARGA DOTCOM.pdf")
