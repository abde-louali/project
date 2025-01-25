import os
from glob import glob
from doctr.io import DocumentFile
from doctr.models import ocr_predictor
import re

# Define the root directory containing the nested folders
root_dir = "images"

# Load the OCR model from docTR
model = ocr_predictor(pretrained=True)

# Keywords to search for
keywords = ["Prénom", "Nom", "Le candidat(e)"]


# Function to clean and normalize extracted values
def normalize_value(value):
    # Remove colons, extra spaces, and leading/trailing whitespace
    value = value.replace(":", "").strip()
    return value


# Function to reformat names into a consistent format
def reformat_name(name_info):
    if "Prénom" in name_info and "Nom" in name_info:
        # Format: "Prénom Nom"
        return f"{normalize_value(name_info['Prénom'])} {normalize_value(name_info['Nom'])}"
    elif "Le candidat(e)" in name_info:
        # Format: "Nom Prénom" (assuming "Le candidat(e)" contains "Nom Prénom")
        full_name = normalize_value(name_info["Le candidat(e)"])
        # Split into parts and reverse if necessary
        parts = full_name.split()
        if len(parts) == 2:
            return f"{parts[1]} {parts[0]}"  # Reformat to "Prénom Nom"
        else:
            return full_name  # Fallback: return as is
    else:
        return None


# Function to extract names using keywords
def extract_names(text, keywords):
    name_info = {}
    lines = text.split("\n")  # Split text into lines

    for line in lines:
        for keyword in keywords:
            if keyword in line:
                # Extract the value after the keyword
                value = line.split(keyword)[-1].strip()
                name_info[keyword] = value
                break  # Stop searching for other keywords in this line

    return name_info


# Function to extract capitalized words
def extract_capital_words(result):
    capital_words = []
    for page in result.pages:
        for block in page.blocks:
            for line in block.lines:
                for word in line.words:
                    word_text = word.value
                    # Check if the word is in capital letters and length > 2
                    if word_text.isupper() and len(word_text) > 2:
                        capital_words.append(word_text)
    return capital_words


# Function to process each image
def process_image(image_path):
    # Load the image using docTR
    doc = DocumentFile.from_images(image_path)

    # Perform OCR
    result = model(doc)

    # Extract the text from the OCR result
    extracted_text = result.render()

    # First attempt: Extract names using keywords
    name_info = extract_names(extracted_text, keywords)

    if name_info:
        # If keywords are found, return the extracted names
        return name_info
    else:
        # Fallback: Extract capitalized words
        capital_words = extract_capital_words(result)

        # Check if there are at least 7 capitalized words
        if len(capital_words) >= 7:
            sixth_word = capital_words[5]
            seventh_word = capital_words[6]
            return {"Prénom": sixth_word, "Nom": seventh_word}
        else:
            return None


# Function to compare names in a folder
def compare_names_in_folder(folder_path):
    # Get all image paths in the folder
    image_paths = glob(os.path.join(folder_path, "*.png"))

    # Extract names from each image
    extracted_names = []
    for image_path in image_paths:
        names = process_image(image_path)
        if names:
            # Normalize and reformat the names
            formatted_name = reformat_name(names)
            if formatted_name:
                extracted_names.append(formatted_name)
                print(f"Extracted names from {os.path.basename(image_path)}: {formatted_name}")
        else:
            print(f"No names extracted from {os.path.basename(image_path)}")

    # Check if all extracted names are the same
    if len(extracted_names) > 0:
        first_name = extracted_names[0]
        for name in extracted_names[1:]:
            if name != first_name:
                return "Incorrect"
        return "Correct"
    else:
        return "No names extracted"


# Traverse through the nested directories
for subdir, _, _ in os.walk(root_dir):
    # Check if the folder contains images
    if glob(os.path.join(subdir, "*.png")):
        print(f"\nProcessing folder: {subdir}")
        # Compare names in the folder
        result = compare_names_in_folder(subdir)
        print(f"Result: {result}")